<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExportReceipt;
use App\Models\ExportReceiptItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\VehicleSalePayment;
use App\Models\Customer;
use App\Models\VehicleSale;
use App\Services\InventoryLogService;


use Barryvdh\DomPDF\Facade\Pdf;

class ExportReceiptController extends Controller
{
    protected string $moduleKey = 'export_receipts';

    /**
     * Check quyền module
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
     * DANH SÁCH PHIẾU XUẤT
     */
    public function index(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $query = ExportReceipt::with(['supplier', 'warehouse', 'createdBy']);

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($from = $request->get('from')) {
            $query->whereDate('export_date', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('export_date', '<=', $to);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($exportType = $request->get('export_type')) {
            $query->where('export_type', $exportType);
        }

        // "Khách hàng" = nhà cung cấp (customer_id trỏ sang suppliers)
        if ($supplierId = $request->get('supplier_id')) {
            $query->where('customer_id', $supplierId);
        }

        $receipts = $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('backend.export_receipts.index', compact(
            'receipts',
            'warehouses',
            'suppliers'
        ));
    }

    /**
     * FORM TẠO PHIẾU XUẤT BÁN BUÔN / CHUYỂN KHO
     */
    public function create()
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();

        // Xe đang trong kho (status = 0)
        $vehiclesInStock = Vehicle::with(['model.brand', 'color', 'warehouse'])
            ->where('status', 0)
            ->orderBy('warehouse_id')
            ->orderBy('id', 'desc')
            ->limit(200)
            ->get();

        return view('backend.export_receipts.create', compact(
            'warehouses',
            'suppliers',
            'models',
            'vehiclesInStock'
        ));
    }

    /**
     * LƯU PHIẾU XUẤT KHO
     * - export_type = sell: bắt buộc chọn nhà cung cấp (đối tác lấy hàng)
     * - export_type = transfer/demo: không cần khách hàng
     */
    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $data = $request->validate([
            'export_date'  => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'export_type'  => 'required|in:sell,transfer,demo',

            // "Khách hàng" = Nhà cung cấp, chỉ bắt buộc khi bán buôn
            'supplier_id'  => 'nullable|exists:suppliers,id',

            'due_date'     => 'nullable|date',
            'note'         => 'nullable|string',

            'items'                    => 'required|array|min:1',
            'items.*.vehicle_id'       => 'required|exists:vehicles,id',
            'items.*.unit_price'       => 'nullable',
            'items.*.discount_amount'  => 'nullable',
            'items.*.license_plate'    => 'nullable|string|max:20',
            'items.*.note'             => 'nullable|string',
        ], [
            'export_date.required'     => 'Vui lòng chọn ngày xuất.',
            'warehouse_id.required'    => 'Vui lòng chọn kho xuất.',
            'export_type.required'     => 'Vui lòng chọn loại xuất kho.',
            'items.required'           => 'Vui lòng chọn ít nhất 1 xe để xuất kho.',
            'items.*.vehicle_id.required' => 'Thiếu thông tin xe xuất kho.',
        ]);

        // Nếu là bán buôn thì phải chọn nhà cung cấp (đối tác)
        if ($data['export_type'] === 'sell' && empty($data['supplier_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('msg-error', 'Vui lòng chọn nhà cung cấp (đối tác nhận xe) cho phiếu xuất bán buôn.');
        }

        DB::transaction(function () use ($data, &$receipt) {

            // Sinh mã PXK auto nếu không nhập tay
            if (empty($data['code'] ?? null)) {
                $last = ExportReceipt::where('code', 'like', 'PXK_%')
                    ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC")
                    ->first();

                if ($last) {
                    $lastNumber = (int) str_replace('PXK_', '', $last->code);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
                $code = 'PXK_' . $nextNumber;
            } else {
                $code = $data['code'];
            }

            $receipt = ExportReceipt::create([
                'code'         => $code,
                'export_date'  => $data['export_date'],
                'warehouse_id' => $data['warehouse_id'],
                'customer_id'  => $data['export_type'] === 'sell'
                    ? ($data['supplier_id'] ?? null)
                    : null, // chuyển kho / demo thì không cần khách hàng
                'export_type'    => $data['export_type'],
                'total_amount'   => 0,
                'paid_amount'    => 0,
                'debt_amount'    => 0,
                'payment_status' => 'unpaid', // chưa thu tiền
                'due_date'       => $data['due_date'] ?? null,
                'note'           => $data['note'] ?? null,
                'created_by'     => auth()->id(),
                'approved_by'    => null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $vehicleId    = $item['vehicle_id'];
                $unitPriceRaw = $item['unit_price'] ?? null;
                $discountRaw  = $item['discount_amount'] ?? null;

                // Bóc số tiền "28.000.000" -> 28000000
                $unitPrice = $unitPriceRaw !== null && $unitPriceRaw !== ''
                    ? (int) preg_replace('/\D/', '', $unitPriceRaw)
                    : 0;

                $discount  = $discountRaw !== null && $discountRaw !== ''
                    ? (int) preg_replace('/\D/', '', $discountRaw)
                    : 0;

                $amount = max($unitPrice - $discount, 0);
                $total += $amount;

                // Lấy thông tin xe
                $vehicle = Vehicle::findOrFail($vehicleId);

                // Tạo dòng chi tiết phiếu xuất
                ExportReceiptItem::create([
                    'export_receipt_id' => $receipt->id,
                    'vehicle_id'        => $vehicleId,
                    'model_id'          => $vehicle->model_id,
                    'quantity'          => 1,
                    'unit_price'        => $unitPrice,
                    'discount_amount'   => $discount,
                    'amount'            => $amount,
                    'license_plate'     => $item['license_plate'] ?? $vehicle->license_plate,
                    'note'              => $item['note'] ?? null,
                ]);

                // ======================
                // CẬP NHẬT XE THEO LOẠI PHIẾU XUẤT
                // ======================
                if ($data['export_type'] === 'sell') {
                    // BÁN BUÔN (xuất khỏi kho, coi như đã bán cho NCC)
                    $vehicle->status     = 'sold_wholesale';
                    $vehicle->sale_price = $unitPrice;
                    $vehicle->sale_date  = $data['export_date'];

                    // Ghi chú thêm cho dễ tra sau này
                    $vehicle->note = trim(
                        ($vehicle->note ? $vehicle->note . ' | ' : '') .
                        'Xuất bán buôn PXK ' . $receipt->code
                    );

                } elseif ($data['export_type'] === 'transfer') {
                    // CHUYỂN KHO
                    // - tùy mô hình, anh có thể:
                    //   + Giữ status = in_stock, chỉ thay warehouse_id nếu biết kho đích
                    //   + Hoặc status = 'transfer' nếu muốn phân biệt rõ
                    $vehicle->status = 'in_stock'; // hoặc 'transfer' nếu anh thích
                    // nếu sau này có cột kho đích thì update tại đây
                    $vehicle->note = trim(
                        ($vehicle->note ? $vehicle->note . ' | ' : '') .
                        'Chuyển kho PXK ' . $receipt->code
                    );

                } elseif ($data['export_type'] === 'demo') {
                    // XE DEMO / SỰ KIỆN
                    $vehicle->status = 'demo';
                    $vehicle->note = trim(
                        ($vehicle->note ? $vehicle->note . ' | ' : '') .
                        'Xe demo PXK ' . $receipt->code
                    );
                }

                // Lưu xe
                $vehicle->save();
                   // ===== GHI LOG XUẤT KHO =====
                   InventoryLogService::logExport(
                        $vehicle,
                        $receipt,
                        'Xuất kho phiếu ' . $receipt->code
                    );

            }

            // Cập nhật tổng tiền + nợ
            $receipt->update([
                'total_amount'   => $total,
                'debt_amount'    => $total,  // mặc định chưa thu tiền
                'paid_amount'    => 0,
                'payment_status' => $total > 0 ? 'unpaid' : 'paid',
            ]);
        });

        return redirect()
            ->route('admin.export_receipts.show', $receipt->id)
            ->with('msg-success', 'Đã tạo phiếu xuất kho: ' . $receipt->code);
    }

    /**
     * XEM CHI TIẾT PHIẾU XUẤT
     */
    public function show(ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $exportReceipt->load([
            'supplier',
            'warehouse',
            'items.vehicle.model.brand',
            'items.vehicle.color',
            'createdBy',
            'approvedBy',
        ]);

        // Tổng số xe + tổng tiền
        $totalVehicles = $exportReceipt->items->count();
        $totalAmount   = $exportReceipt->items->sum('amount');

        return view('backend.export_receipts.show', compact(
            'exportReceipt',
            'totalVehicles',
            'totalAmount'
        ));
    }

    /**
     * SỬA PHIẾU XUẤT
     * - KHÔNG CHO SỬA KHI payment_status = 'paid_docs' (đã giao giấy tờ)
     */
    public function edit(ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        // KHÔNG CHO SỬA KHI ĐÃ GIAO GIẤY TỜ
        if ($exportReceipt->payment_status === 'paid_docs') {
            return redirect()
                ->route('admin.export_receipts.show', $exportReceipt->id)
                ->with('msg-error', 'Phiếu xuất này đã được đánh dấu ĐÃ GIAO GIẤY TỜ, không được phép sửa.');
        }

        $exportReceipt->load([
            'warehouse',
            'supplier',
            'items.vehicle.model.brand',
            'items.vehicle.color',
            'createdBy',
            'approvedBy',
        ]);

        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        // Danh sách xe được dùng trong phiếu + các xe còn trong kho
        $vehicleIdsInReceipt = $exportReceipt->items->pluck('vehicle_id')->filter()->unique();

        $vehicles = Vehicle::with(['model.brand', 'color', 'warehouse'])
            ->where(function ($q) use ($vehicleIdsInReceipt) {
                if ($vehicleIdsInReceipt->isNotEmpty()) {
                    $q->whereIn('id', $vehicleIdsInReceipt)
                      ->orWhere('status', 0);          // xe đang trong kho
                } else {
                    $q->where('status', 0);
                }
            })
            ->orderBy('frame_no')
            ->get();

        return view('backend.export_receipts.edit', compact(
            'exportReceipt',
            'warehouses',
            'suppliers',
            'vehicles'
        ));
    }

    /**
     * CẬP NHẬT PHIẾU XUẤT
     */
    public function update(Request $request, ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        // Không cho update nếu đã giao giấy tờ
        if ($exportReceipt->payment_status === 'paid_docs') {
            return redirect()
                ->route('admin.export_receipts.show', $exportReceipt->id)
                ->with('msg-error', 'Phiếu xuất này đã được đánh dấu ĐÃ GIAO GIẤY TỜ, không được phép sửa.');
        }

        $data = $request->validate([
            'export_date'  => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'export_type'  => 'required|in:sell,transfer,demo',
            'supplier_id'  => 'nullable|exists:suppliers,id',
            'due_date'     => 'nullable|date',
            'note'         => 'nullable|string',

            'items'                    => 'required|array|min:1',
            'items.*.vehicle_id'       => 'required|exists:vehicles,id',
            'items.*.unit_price'       => 'nullable',
            'items.*.note'             => 'nullable|string',
        ], [
            'export_date.required'     => 'Vui lòng chọn ngày xuất.',
            'warehouse_id.required'    => 'Vui lòng chọn kho xuất.',
            'export_type.required'     => 'Vui lòng chọn loại xuất kho.',
            'items.required'           => 'Vui lòng chọn ít nhất 1 xe để xuất kho.',
            'items.*.vehicle_id.required' => 'Thiếu thông tin xe xuất kho.',
        ]);

        if ($data['export_type'] === 'sell' && empty($data['supplier_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('msg-error', 'Vui lòng chọn nhà cung cấp (đối tác nhận xe) cho phiếu xuất bán buôn.');
        }

        DB::transaction(function () use ($data, $exportReceipt) {

            // Cập nhật thông tin chung
            $exportReceipt->update([
                'export_date'  => $data['export_date'],
                'warehouse_id' => $data['warehouse_id'],
                'customer_id'  => $data['export_type'] === 'sell'
                    ? ($data['supplier_id'] ?? null)
                    : null,
                'export_type'  => $data['export_type'],
                'due_date'     => $data['due_date'] ?? null,
                'note'         => $data['note'] ?? null,
            ]);

            // Xóa toàn bộ items cũ rồi tạo lại cho đơn giản
            ExportReceiptItem::where('export_receipt_id', $exportReceipt->id)->delete();

            $total = 0;

            foreach ($data['items'] as $item) {
                $vehicleId    = $item['vehicle_id'];
                $unitPriceRaw = $item['unit_price'] ?? null;

                $unitPrice = $unitPriceRaw !== null && $unitPriceRaw !== ''
                    ? (int) preg_replace('/\D/', '', $unitPriceRaw)
                    : 0;

                $amount = $unitPrice;
                $total += $amount;

                $vehicle = Vehicle::findOrFail($vehicleId);

                ExportReceiptItem::create([
                    'export_receipt_id' => $exportReceipt->id,
                    'vehicle_id'        => $vehicleId,
                    'model_id'          => $vehicle->model_id,
                    'quantity'          => 1,
                    'unit_price'        => $unitPrice,
                    'discount_amount'   => 0,
                    'amount'            => $amount,
                    'license_plate'     => $vehicle->license_plate,
                    'note'              => $item['note'] ?? null,
                ]);

                // Cập nhật lại trạng thái xe (phòng TH chuyển xe khác)
                $vehicle->status     = 1;
                $vehicle->sale_price = $unitPrice;
                $vehicle->sale_date  = $data['export_date'];
                $vehicle->save();
            }

            // Giữ nguyên paid_amount, cập nhật total + debt + status
            $oldPaid = $exportReceipt->paid_amount ?? 0;
            $debt    = max($total - $oldPaid, 0);

            if ($oldPaid <= 0) {
                $status = 'unpaid';
            } elseif ($debt <= 0) {
                $status = 'paid'; // Không thể là paid_docs vì đã chặn ở trên
            } else {
                $status = 'partial';
            }

            $exportReceipt->update([
                'total_amount'   => $total,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);
        });

        return redirect()
            ->route('admin.export_receipts.show', $exportReceipt->id)
            ->with('msg-success', 'Cập nhật phiếu xuất kho thành công.');
    }

    /**
     * XÓA PHIẾU XUẤT
     */
    public function destroy(ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('delete')) {
            return $resp;
        }

        // Nếu đã giao giấy tờ thì không cho xóa (anh có thể bỏ nếu muốn)
        if ($exportReceipt->payment_status === 'paid_docs') {
            return redirect()
                ->back()
                ->with('msg-error', 'Phiếu đã giao giấy tờ, không được phép xóa.');
        }

        $exportReceipt->items()->delete();
        $exportReceipt->delete();

        return redirect()
            ->route('admin.export_receipts.index')
            ->with('msg-success', 'Xóa phiếu xuất kho thành công.');
    }

    /**
     * ĐÁNH DẤU ĐÃ NHẬN TIỀN
     */
    public function markPaid(ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        // Nếu đã paid_docs thì thôi, không cho chỉnh nữa
        if ($exportReceipt->payment_status === 'paid_docs') {
            return redirect()
                ->back()
                ->with('msg-error', 'Phiếu này đã được đánh dấu ĐÃ NHẬN TIỀN & GIAO GIẤY TỜ, không thể sửa trạng thái thanh toán.');
        }

        // Tính tổng tiền: ưu tiên amount, fallback unit_price
        $totalFromAmount = $exportReceipt->items()->sum('amount');
        $totalFromPrice  = $exportReceipt->items()->sum('unit_price');
        $total           = $totalFromAmount ?: $totalFromPrice ?: 0;

        $exportReceipt->update([
            'payment_status' => 'paid',   // Đã thu tiền, chưa giao giấy
            'paid_amount'    => $total,
            'debt_amount'    => 0,
        ]);

        return redirect()
            ->back()
            ->with('msg-success', 'Đã đánh dấu phiếu xuất ĐÃ NHẬN ĐỦ TIỀN.');
    }

    /**
     * ĐÁNH DẤU ĐÃ GIAO GIẤY TỜ
     * - Không tạo cột mới, chỉ đổi payment_status -> paid_docs
     */
    public function markDocsDelivered(ExportReceipt $exportReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        if ($exportReceipt->payment_status !== 'paid') {
            return redirect()
                ->back()
                ->with('msg-error', 'Chỉ được giao giấy tờ khi phiếu đã được đánh dấu ĐÃ NHẬN ĐỦ TIỀN.');
        }

        $exportReceipt->update([
            'payment_status' => 'paid_docs',
        ]);

        return redirect()
            ->back()
            ->with('msg-success', 'Đã đánh dấu ĐÃ GIAO ĐỦ GIẤY TỜ cho phiếu xuất này.');
    }



            // =======================================================
        // =============== BÁN LẺ XE (HÓA ĐƠN BÁN LẺ) ============
        // =======================================================


        /**
         * DANH SÁCH HÓA ĐƠN BÁN LẺ
         */
        public function indexRetail(Request $request)
        {
            // Nếu anh muốn dùng quyền riêng module vehicle_sales
            // có thể làm 1 hàm authorize riêng, tạm thời vẫn dùng authorizeModule('read')
            if ($resp = $this->authorizeModule('read')) {
                return $resp;
            }

            $query = VehicleSale::with([
                'vehicle.model.brand',
                'customer',
            ]);

            if ($code = $request->get('code')) {
                $query->where('code', 'like', '%' . $code . '%');
            }

            if ($from = $request->get('from')) {
                $query->whereDate('sale_date', '>=', $from);
            }

            if ($to = $request->get('to')) {
                $query->whereDate('sale_date', '<=', $to);
            }

            if ($phone = $request->get('phone')) {
                $query->whereHas('customer', function ($q) use ($phone) {
                    $q->where('phone', 'like', '%' . $phone . '%');
                });
            }

            if ($status = $request->get('payment_status')) {
                $query->where('payment_status', $status);
            }

            $sales = $query->orderByDesc('sale_date')
                ->orderByDesc('id')
                ->paginate(20);

            return view('backend.vehicle_sales.index', compact('sales'));
        }



        /**
         * FORM BÁN LẺ
         */
        public function createRetail()
        {
            if ($resp = $this->authorizeModule('create')) {
                return $resp;
            }

            $vehicles = Vehicle::with(['model.brand', 'color', 'warehouse'])
                ->where(function ($q) {
                    $q->where('status', 0)
                    ->orWhere('status', 'in_stock');
                })
                ->orderBy('warehouse_id')
                ->orderBy('frame_no')
                ->get();

            $customers = Customer::orderBy('name')->limit(50)->get();

            return view('backend.vehicle_sales.create', compact('vehicles', 'customers'));
        }





}
