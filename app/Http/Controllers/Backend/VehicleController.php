<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\VehicleModel;
use App\Models\Color;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\ImportReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Module key dùng cho phân quyền CRUD theo bảng module_permissions
     * module_key = 'vehicles'
     */
    protected string $moduleKey = 'vehicles';

    /**
     * Hàm check quyền cho từng action
     * - $action: create | read | update | delete
     * - Nếu không có quyền: redirect back + flash msg-error
     * - Nếu có quyền: return null (controller tiếp tục chạy)
     */
    protected function authorizeModule(string $action)
    {
        $user = Auth::user();

        if (!$user || !$user->canModule($this->moduleKey, $action)) {
            return redirect()->back()
                ->with('msg-error', 'Tài khoản của bạn không có quyền truy cập chức năng này.');
        }

        return null;
    }

    /**
     * DANH SÁCH XE
     */
    public function index(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $importReceiptId = $request->query('import_receipt_id');

        $query = Vehicle::with([
                'model.brand',
                'brand',
                'color',
                'warehouse',
                'supplier',
                'customer',
                'importReceipt',
            ])
            ->orderBy('created_at', 'desc');

        // Nếu có filter theo phiếu nhập thì chỉ lấy xe của phiếu đó
        $importReceipt = null;
        if (!empty($importReceiptId)) {
            $query->where('import_receipt_id', $importReceiptId);
            $importReceipt = ImportReceipt::with(['supplier', 'warehouse'])
                ->find($importReceiptId);
        }

        $vehicles = $query->get();

        // Tính tổng xe + tổng tiền nhập
        $totalVehicles = $vehicles->count();
        $totalPurchase = $vehicles->sum('purchase_price');

        return view('backend.vehicles.index', compact(
            'vehicles',
            'importReceipt',
            'importReceiptId',
            'totalVehicles',
            'totalPurchase'
        ));
    }

    /**
     * FORM THÊM XE
     */
    public function create(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $brands     = Brand::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $colors     = Color::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        // nếu vào link: /admin/xe/tao-moi?import_receipt_id=5
        $importReceiptId = $request->query('import_receipt_id');

        return view('backend.vehicles.create', compact(
            'brands', 'models', 'colors', 'warehouses', 'suppliers', 'importReceiptId'
        ));
    }

    /**
     * LƯU XE MỚI
     */
    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $request->validate([
            // brand_id: Hãng xe, LƯU trực tiếp vào vehicles
            'brand_id'       => 'required|exists:brands,id',
            'model_id'       => 'required|exists:vehicle_models,id',
            'color_id'       => 'nullable|exists:colors,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',

            'frame_no'       => 'required|string|max:100|unique:vehicles,frame_no',
            'engine_no'      => 'nullable|string|max:100',
            'year'           => 'nullable|integer',

            'purchase_price' => 'nullable', // sẽ tự bóc số
            'status'         => 'nullable|string|max:50',
            'license_plate'  => 'nullable|string|max:20',
            'note'           => 'nullable|string',
        ], [
            'brand_id.required'      => 'Vui lòng chọn hãng xe.',
            'brand_id.exists'        => 'Hãng xe không hợp lệ.',
            'model_id.required'      => 'Vui lòng chọn dòng xe.',
            'model_id.exists'        => 'Dòng xe không hợp lệ.',
            'warehouse_id.required'  => 'Vui lòng chọn kho.',
            'warehouse_id.exists'    => 'Kho không hợp lệ.',
            'supplier_id.exists'     => 'Nhà cung cấp không hợp lệ.',
            'frame_no.required'      => 'Vui lòng nhập số khung.',
            'frame_no.unique'        => 'Số khung này đã tồn tại trong hệ thống.',
        ]);

        // Bóc số tiền "28.000.000" -> 28000000 (decimal sẽ lưu 28000000.00)
        $purchasePriceRaw = $request->input('purchase_price');
        if ($purchasePriceRaw !== null && $purchasePriceRaw !== '') {
            $purchasePrice = (int) preg_replace('/\D/', '', $purchasePriceRaw);
        } else {
            $purchasePrice = null;
        }

        $vehicle = new Vehicle();
        $vehicle->brand_id       = $request->brand_id;   // ⭐ LƯU HÃNG XE
        $vehicle->model_id       = $request->model_id;
        $vehicle->color_id       = $request->color_id;
        $vehicle->warehouse_id   = $request->warehouse_id;
        $vehicle->supplier_id    = $request->supplier_id;
        $vehicle->frame_no       = trim($request->frame_no);
        $vehicle->engine_no      = trim($request->engine_no);
        $vehicle->year           = $request->year;
        $vehicle->purchase_price = $purchasePrice;
        // dùng in_stock làm mặc định
        $vehicle->status         = $request->status ?: 'in_stock';
        $vehicle->license_plate  = $request->license_plate;
        $vehicle->note           = $request->note;

        // gắn với phiếu nhập nếu có
        if (!$request->filled('import_receipt_id')) {
            // Tạo phiếu nhập tự động
            $autoReceipt = new ImportReceipt();

            // sinh mã PNK_N+1
            $last = ImportReceipt::where('code', 'like', 'PNK_%')
                ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC")
                ->first();

            if ($last) {
                $lastNumber = (int) str_replace('PNK_', '', $last->code);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            $autoCode = 'PNK_' . $nextNumber;

            $autoReceipt->code         = $autoCode;
            $autoReceipt->import_date  = now()->toDateString();
            $autoReceipt->warehouse_id = $request->warehouse_id;
            $autoReceipt->supplier_id  = $request->supplier_id;
            $autoReceipt->note         = 'Phiếu nhập tự động khi thêm xe trực tiếp.';
            $autoReceipt->created_by   = auth()->id();
            $autoReceipt->save();

            $vehicle->import_receipt_id = $autoReceipt->id;
        } else {
            $vehicle->import_receipt_id = $request->import_receipt_id;
        }

        // Mặc định 2 cờ
        $vehicle->supplier_paid            = 0;
        $vehicle->supplier_paid_at         = null;
        $vehicle->registration_received    = 0;
        $vehicle->registration_received_at = null;

        $vehicle->save();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('msg-success', 'Đã thêm xe mới thành công.');
    }

    /**
     * FORM SỬA XE
     */
    public function edit(Vehicle $vehicle)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $brands     = Brand::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $colors     = Color::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('backend.vehicles.edit', compact(
            'vehicle',
            'brands',
            'models',
            'colors',
            'warehouses',
            'suppliers'
        ));
    }

    /**
     * CẬP NHẬT THÔNG TIN XE
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $request->validate([
            'brand_id'      => 'required|exists:brands,id',
            'model_id'      => 'required|exists:vehicle_models,id',
            'color_id'      => 'nullable|exists:colors,id',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'supplier_id'   => 'nullable|exists:suppliers,id',

            'frame_no'      => 'required|string|max:100|unique:vehicles,frame_no,' . $vehicle->id,
            'engine_no'     => 'nullable|string|max:100',
            'year'          => 'nullable|integer',
            'license_plate' => 'nullable|string|max:20',
            'status'        => 'nullable|string|max:50',
            'note'          => 'nullable|string',
            'purchase_price'=> 'nullable',
        ], [
            'brand_id.required'      => 'Vui lòng chọn hãng xe.',
            'brand_id.exists'        => 'Hãng xe không hợp lệ.',
            'model_id.required'      => 'Vui lòng chọn dòng xe.',
            'model_id.exists'        => 'Dòng xe không hợp lệ.',
            'color_id.exists'        => 'Màu xe không hợp lệ.',
            'warehouse_id.exists'    => 'Kho không hợp lệ.',
            'supplier_id.exists'     => 'Nhà cung cấp không hợp lệ.',
            'frame_no.required'      => 'Vui lòng nhập số khung.',
            'frame_no.unique'        => 'Số khung này đã tồn tại trong hệ thống.',
        ]);

        // bóc tiền
        $purchasePriceRaw = $request->input('purchase_price');
        if ($purchasePriceRaw !== null && $purchasePriceRaw !== '') {
            $purchasePrice = (int) preg_replace('/\D/', '', $purchasePriceRaw);
        } else {
            $purchasePrice = null;
        }

        $vehicle->brand_id       = $request->brand_id;  // ⭐ CẬP NHẬT HÃNG XE
        $vehicle->model_id       = $request->model_id;
        $vehicle->color_id       = $request->color_id;
        $vehicle->warehouse_id   = $request->warehouse_id;
        $vehicle->supplier_id    = $request->supplier_id;
        $vehicle->frame_no       = trim($request->frame_no);
        $vehicle->engine_no      = trim($request->engine_no);
        $vehicle->year           = $request->year;
        $vehicle->license_plate  = trim($request->license_plate);
        $vehicle->status         = $request->status ?? $vehicle->status;
        $vehicle->note           = $request->note;
        $vehicle->purchase_price = $purchasePrice;

        $vehicle->save();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('msg-success', 'Cập nhật thông tin xe thành công.');
    }

    /**
     * XÓA XE
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($resp = $this->authorizeModule('delete')) {
            return $resp;
        }

        $vehicle->delete();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('msg-success', 'Đã xóa xe thành công.');
    }

    /**
     * FORM THÊM XE THEO PHIẾU NHẬP
     */
    public function createForImport(Request $request, ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $importReceipt->load('vehicles');

        $totalVehicles = $importReceipt->vehicles->count();
        $paidVehicles  = $importReceipt->vehicles->where('supplier_paid', 1)->count();
        $isFullyPaid   = $totalVehicles > 0 && $paidVehicles == $totalVehicles;

        if ($isFullyPaid) {
            return redirect()
                ->route('admin.import_receipts.show', $importReceipt->id)
                ->with('msg-error', 'Phiếu nhập đã thanh toán cho nhà cung cấp, không thể thêm xe mới.');
        }

        $brands     = Brand::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $colors     = Color::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        // supplier lấy luôn từ phiếu nhập, không cho sửa
        $supplier = $importReceipt->supplier;

        return view('backend.import_receipts.add_vehicle', compact(
            'importReceipt', 'brands', 'models', 'colors', 'warehouses', 'supplier'
        ));
    }

    /**
     * LƯU NHIỀU XE VÀO 1 PHIẾU NHẬP
     */
    public function storeForImport(Request $request, ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $importReceipt->load('vehicles');

        $totalVehicles = $importReceipt->vehicles->count();
        $paidVehicles  = $importReceipt->vehicles->where('supplier_paid', 1)->count();
        $isFullyPaid   = $totalVehicles > 0 && $paidVehicles == $totalVehicles;

        if ($isFullyPaid) {
            return redirect()
                ->route('admin.import_receipts.show', $importReceipt->id)
                ->with('msg-error', 'Phiếu nhập đã thanh toán cho nhà cung cấp, không thể thêm xe mới.');
        }

        $data = $request->validate([
            'vehicles'                      => 'required|array|min:1',
            'vehicles.*.brand_id'           => 'required|exists:brands,id',
            'vehicles.*.model_id'           => 'required|exists:vehicle_models,id',
            'vehicles.*.color_id'           => 'nullable|exists:colors,id',
            'vehicles.*.warehouse_id'       => 'required|exists:warehouses,id',
            'vehicles.*.frame_no'           => 'required|string|max:100|distinct|unique:vehicles,frame_no',
            'vehicles.*.engine_no'          => 'nullable|string|max:100',
            'vehicles.*.year'               => 'nullable|integer',
            'vehicles.*.purchase_price'     => 'nullable',
            'vehicles.*.status'             => 'nullable|string|max:50',
            'vehicles.*.license_plate'      => 'nullable|string|max:20',
            'vehicles.*.note'               => 'nullable|string',
        ], [
            'vehicles.required'                 => 'Vui lòng nhập ít nhất 1 xe.',
            'vehicles.*.brand_id.required'      => 'Vui lòng chọn hãng xe.',
            'vehicles.*.model_id.required'      => 'Vui lòng chọn dòng xe.',
            'vehicles.*.warehouse_id.required'  => 'Vui lòng chọn kho.',
            'vehicles.*.frame_no.required'      => 'Vui lòng nhập số khung.',
            'vehicles.*.frame_no.unique'        => 'Số khung đã tồn tại trong hệ thống.',
        ]);

        $count = 0;

        foreach ($data['vehicles'] as $v) {
            // nếu form trống thì bỏ qua
            $allEmpty = empty($v['brand_id'])
                && empty($v['model_id'])
                && empty($v['frame_no']);

            if ($allEmpty) {
                continue;
            }

            // bóc giá nhập từng xe (nếu có)
            $price = null;
            if (!empty($v['purchase_price'])) {
                $price = (int) preg_replace('/\D/', '', $v['purchase_price']);
            }

            $vehicle = new Vehicle();
            $vehicle->brand_id          = $v['brand_id'];           // ⭐ LƯU HÃNG XE CHO TỪNG XE
            $vehicle->model_id          = $v['model_id'];
            $vehicle->color_id          = $v['color_id'] ?? null;
            $vehicle->warehouse_id      = $v['warehouse_id'];
            $vehicle->supplier_id       = $importReceipt->supplier_id;
            $vehicle->import_receipt_id = $importReceipt->id;
            $vehicle->frame_no          = $v['frame_no'];
            $vehicle->engine_no         = $v['engine_no'] ?? null;
            $vehicle->year              = $v['year'] ?? null;
            $vehicle->purchase_price    = $price;
            $vehicle->status            = $v['status'] ?: 'in_stock';
            $vehicle->license_plate     = $v['license_plate'] ?? null;
            $vehicle->note              = $v['note'] ?? null;

            // mặc định chưa thanh toán / chưa nhận đăng kiểm
            $vehicle->supplier_paid            = 0;
            $vehicle->supplier_paid_at         = null;
            $vehicle->registration_received    = 0;
            $vehicle->registration_received_at = null;

            $vehicle->save();
            $count++;
        }

        if ($count == 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('msg-error', 'Chưa có xe hợp lệ nào được nhập.');
        }

        return redirect()
            ->route('admin.import_receipts.show', $importReceipt->id)
            ->with('msg-success', "Đã thêm $count xe vào phiếu nhập {$importReceipt->code}.");
    }

    /**
     * KẾT THÚC DEMO → CHO XE VỀ KHO (CÁCH 1)
     * - Chỉ cho phép khi status = 'demo'
     */
       /**
     * KẾT THÚC DEMO → CHO XE VỀ TRONG KHO
     * - Chỉ áp dụng cho xe đang ở trạng thái demo / demo_out
     * - Cho phép đổi kho nếu muốn
     */
    public function endDemo(Request $request, Vehicle $vehicle)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        // Chỉ cho phép kết thúc demo nếu xe đang ở trạng thái demo
        if (!in_array($vehicle->status, ['demo', 'demo_out'])) {
            return redirect()
                ->back()
                ->with('msg-error', 'Xe này hiện không ở trạng thái DEMO, không thể kết thúc demo.');
        }

        // Cho phép chọn lại kho nếu cần (không bắt buộc)
        $data = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'note'         => 'nullable|string',
        ]);

        if (!empty($data['warehouse_id'])) {
            $vehicle->warehouse_id = $data['warehouse_id'];
        }

        // Xe quay về trạng thái trong kho
        $vehicle->status = 'in_stock';

        // Đảm bảo không dính dữ liệu bán lẻ nào
        $vehicle->sale_price = null;
        $vehicle->sale_date  = null;
        $vehicle->customer_id = null;

        // Ghi chú thêm nếu có
        if (!empty($data['note'])) {
            // nối thêm ghi chú cũ cho dễ theo dõi lịch sử
            $oldNote = $vehicle->note ? ($vehicle->note . " | ") : "";
            $vehicle->note = $oldNote . '[Kết thúc demo] ' . $data['note'];
        }

        $vehicle->save();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('msg-success', 'Đã kết thúc DEMO, xe đã được chuyển về trạng thái TRONG KHO.');
    }


    public function show($id)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $vehicle = Vehicle::with([
            'model.brand',
            'color',
            'warehouse',
            'supplier',
            'importReceipt.supplier',
            'importReceipt.warehouse',
            'exportReceiptItems.exportReceipt.supplier',
            'retailSale.customer',   // <<< quan trọng
        ])->findOrFail($id);

        // Lấy phiếu xuất gần nhất (nếu có)
        $lastExport = $vehicle->exportReceiptItems
            ->filter(function ($item) {
                return $item->exportReceipt !== null;
            })
            ->sortByDesc(function ($item) {
                return $item->exportReceipt->export_date ?? $item->created_at;
            })
            ->first();

        return view('backend.vehicles.show', compact('vehicle', 'lastExport'));
    }



}
