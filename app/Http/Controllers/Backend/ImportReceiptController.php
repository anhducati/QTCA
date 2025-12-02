<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ImportReceipt;
use App\Models\ImportReceiptItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryLogService;


class ImportReceiptController extends Controller
{
    protected string $moduleKey = 'import_receipts';

    /**
     * Kiểm tra quyền của module (tạo / sửa / xóa / xem)
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
     * DANH SÁCH PHIẾU NHẬP
     */
    public function index(Request $request)
    {
        // Nếu muốn check quyền đọc:
        // if ($resp = $this->authorizeModule('read')) {
        //     return $resp;
        // }

        $query = ImportReceipt::with(['supplier', 'warehouse', 'createdBy', 'vehicles']);

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($from = $request->get('from')) {
            $query->whereDate('import_date', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('import_date', '<=', $to);
        }

        $receipts = $query->orderByDesc('import_date')
            ->orderByDesc('id')
            ->paginate(20);

        return view('backend.import_receipts.index', compact('receipts'));
    }

    /**
     * FORM TẠO PHIẾU NHẬP
     */
    public function create()
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        $suppliers  = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();

        return view('backend.import_receipts.create', compact('suppliers', 'warehouses', 'models'));
    }

    /**
     * LƯU PHIẾU NHẬP MỚI
     */
    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        // Validate
        $request->validate([
            'import_date'  => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id'  => 'required|exists:suppliers,id',
        ], [
            'import_date.required'  => 'Ngày nhập không được bỏ trống',
            'warehouse_id.required' => 'Vui lòng chọn kho nhập',
            'supplier_id.required'  => 'Vui lòng chọn nhà cung cấp',
        ]);

        // ========= AUTO GENERATE CODE ==========
        if (empty($request->code)) {
            // lấy số lớn nhất của các mã đã sinh
            $lastReceipt = ImportReceipt::where('code', 'LIKE', 'PNK_%')
                ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC")
                ->first();

            if ($lastReceipt) {
                // Tách số N
                $lastNumber = (int) str_replace('PNK_', '', $lastReceipt->code);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $generatedCode = 'PNK_' . $nextNumber;
        } else {
            $generatedCode = $request->code;
        }

        // ====== Tạo phiếu nhập ======
        $receipt = ImportReceipt::create([
            'code'        => $generatedCode,
            'import_date' => $request->import_date,
            'warehouse_id'=> $request->warehouse_id,
            'supplier_id' => $request->supplier_id,
            'note'        => $request->note,
            'created_by'  => auth()->id(),
        ]);

        return redirect()
            ->route('admin.import_receipts.show', $receipt->id)
            ->with('msg-success', 'Tạo phiếu nhập thành công! Mã phiếu: ' . $generatedCode);
    }

    /**
     * XEM CHI TIẾT PHIẾU NHẬP
     */
    public function show(ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $importReceipt->load([
            'supplier',
            'warehouse',
            'items.model',
            'items.vehicle',
            'vehicles',   // danh sách xe (Vehicle) thuộc phiếu này
            'createdBy',  // nếu có quan hệ createdBy trong model
        ]);

        return view('backend.import_receipts.show', compact('importReceipt'));
    }

    /**
     * FORM SỬA PHIẾU NHẬP
     */
    public function edit(ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $importReceipt->load('items');

        $suppliers  = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();

        return view('backend.import_receipts.edit', compact('importReceipt', 'suppliers', 'warehouses', 'models'));
    }

    /**
     * CẬP NHẬT PHIẾU NHẬP
     */
    public function update(Request $request, ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $data = $request->validate([
            'import_date'  => 'required|date',
            'supplier_id'  => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note'         => 'nullable|string',

            'items'                         => 'required|array|min:1',
            'items.*.id'                    => 'nullable|exists:import_receipt_items,id',
            'items.*.vehicle_id'            => 'nullable|exists:vehicles,id',
            'items.*.model_id'              => 'required|exists:vehicle_models,id',
            'items.*.quantity'              => 'nullable|integer|min:1',
            'items.*.unit_price'            => 'nullable|numeric',
            'items.*.vat_percent'           => 'nullable|numeric',
            'items.*.amount'                => 'nullable|numeric',
            'items.*.note'                  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $importReceipt) {
            $importReceipt->update([
                'import_date'  => $data['import_date'],
                'supplier_id'  => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'note'         => $data['note'] ?? null,
            ]);

            $keepIds = [];
            $total   = 0;

            foreach ($data['items'] as $item) {
                $qty       = $item['quantity']   ?? 1;
                $unitPrice = $item['unit_price'] ?? 0;
                $amount    = $item['amount']     ?? ($qty * $unitPrice);

                if (!empty($item['id'])) {
                    $detail = ImportReceiptItem::where('import_receipt_id', $importReceipt->id)
                        ->where('id', $item['id'])
                        ->first();

                    if ($detail) {
                        $detail->update([
                            'vehicle_id'  => $item['vehicle_id'] ?? null,
                            'model_id'    => $item['model_id'],
                            'quantity'    => $qty,
                            'unit_price'  => $unitPrice,
                            'vat_percent' => $item['vat_percent'] ?? null,
                            'amount'      => $amount,
                            'note'        => $item['note'] ?? null,
                        ]);
                        $keepIds[] = $detail->id;
                    }
                } else {
                    $detail = ImportReceiptItem::create([
                        'import_receipt_id' => $importReceipt->id,
                        'vehicle_id'        => $item['vehicle_id'] ?? null,
                        'model_id'          => $item['model_id'],
                        'quantity'          => $qty,
                        'unit_price'        => $unitPrice,
                        'vat_percent'       => $item['vat_percent'] ?? null,
                        'amount'            => $amount,
                        'note'              => $item['note'] ?? null,
                    ]);
                    $keepIds[] = $detail->id;
                }

                $total += $amount;
            }

            ImportReceiptItem::where('import_receipt_id', $importReceipt->id)
                ->whereNotIn('id', $keepIds)
                ->delete();

            $importReceipt->update(['total_amount' => $total]);
        });

        return redirect()->route('admin.import_receipts.index')
            ->with('success', 'Cập nhật phiếu nhập thành công');
    }

    /**
     * XÓA PHIẾU NHẬP
     */
    public function destroy(ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('delete')) {
            return $resp;
        }

        $importReceipt->delete();

        return redirect()->route('admin.import_receipts.index')
            ->with('success', 'Xóa phiếu nhập thành công');
    }

    /**
     * ĐÁNH DẤU ĐÃ THANH TOÁN NCC CHO TOÀN BỘ XE THUỘC PHIẾU
     */
    public function markPaid(ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $vehiclesQuery = Vehicle::where('import_receipt_id', $importReceipt->id);

        if (!$vehiclesQuery->exists()) {
            return redirect()
                ->back()
                ->with('msg-error', 'Phiếu nhập này chưa có xe nào, không thể đánh dấu đã thanh toán.');
        }

        $today = now()->toDateString();

        $vehiclesQuery->update([
            'supplier_paid'    => 1,
            'supplier_paid_at' => $today,
        ]);

        return redirect()
            ->back()
            ->with('msg-success', 'Đã đánh dấu ĐÃ THANH TOÁN NCC cho toàn bộ xe thuộc phiếu nhập này.');
    }

    /**
     * ĐÁNH DẤU ĐÃ NHẬN GIẤY TỜ CHO TOÀN BỘ XE THUỘC PHIẾU
     */
    public function markDocsReceived(ImportReceipt $importReceipt)
    {
        if ($resp = $this->authorizeModule('update')) {
            return $resp;
        }

        $vehicles = Vehicle::where('import_receipt_id', $importReceipt->id)->get();

        if ($vehicles->count() === 0) {
            return redirect()
                ->back()
                ->with('msg-error', 'Phiếu nhập này chưa có xe nào, không thể đánh dấu nhận giấy tờ.');
        }

        // Chỉ cho nhận giấy tờ khi tất cả xe đã thanh toán NCC
        $allPaid = $vehicles->every(function ($v) {
            return (int) $v->supplier_paid === 1;
        });

        if (!$allPaid) {
            return redirect()
                ->back()
                ->with('msg-error', 'Chỉ được đánh dấu ĐÃ NHẬN GIẤY TỜ khi toàn bộ xe đã được thanh toán NCC.');
        }

        $today = now()->toDateString();

        Vehicle::where('import_receipt_id', $importReceipt->id)
            ->update([
                'registration_received'    => 1,
                'registration_received_at' => $today,
            ]);

        return redirect()
            ->back()
            ->with('msg-success', 'Đã đánh dấu ĐÃ NHẬN GIẤY TỜ cho toàn bộ xe thuộc phiếu nhập này.');
    }
}
