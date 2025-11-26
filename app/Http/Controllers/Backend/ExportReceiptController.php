<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExportReceipt;
use App\Models\ExportReceiptItem;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExportReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = ExportReceipt::with(['customer', 'warehouse']);

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }
        if ($customer = $request->get('customer')) {
            $query->whereHas('customer', function ($q) use ($customer) {
                $q->where('name', 'like', "%{$customer}%")
                  ->orWhere('phone', 'like', "%{$customer}%");
            });
        }
        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $receipts = $query->orderByDesc('export_date')->orderByDesc('id')->paginate(20);

        return view('backend.export_receipts.index', compact('receipts'));
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $customers  = Customer::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $vehicles   = Vehicle::where('status', 'in_stock')->orderBy('frame_no')->get();

        return view('backend.export_receipts.create', compact('warehouses', 'customers', 'models', 'vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => 'required|string|max:50|unique:export_receipts,code',
            'export_date'   => 'required|date',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'customer_id'   => 'required|exists:customers,id',
            'export_type'   => 'nullable|string|max:50',
            'note'          => 'nullable|string',

            'paid_amount'   => 'nullable|numeric',
            'due_date'      => 'nullable|date',

            'items'                         => 'required|array|min:1',
            'items.*.vehicle_id'            => 'required|exists:vehicles,id',
            'items.*.model_id'              => 'required|exists:vehicle_models,id',
            'items.*.quantity'              => 'nullable|integer|min:1',
            'items.*.unit_price'            => 'required|numeric',
            'items.*.discount_amount'       => 'nullable|numeric',
            'items.*.amount'                => 'nullable|numeric',
            'items.*.license_plate'         => 'nullable|string|max:20',
            'items.*.note'                  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $userId   = Auth::id();
            $paid     = $data['paid_amount'] ?? 0;
            $type     = $data['export_type'] ?? 'sell';

            $receipt = ExportReceipt::create([
                'code'          => $data['code'],
                'export_date'   => $data['export_date'],
                'warehouse_id'  => $data['warehouse_id'],
                'customer_id'   => $data['customer_id'],
                'export_type'   => $type,
                'total_amount'  => 0,
                'paid_amount'   => $paid,
                'debt_amount'   => 0,
                'payment_status'=> 'unpaid',
                'due_date'      => $data['due_date'] ?? null,
                'note'          => $data['note'] ?? null,
                'created_by'    => $userId,
                'approved_by'   => null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $qty     = $item['quantity']        ?? 1;
                $price   = $item['unit_price'];
                $disc    = $item['discount_amount'] ?? 0;
                $amount  = $item['amount'] ?? ($qty * $price - $disc);

                ExportReceiptItem::create([
                    'export_receipt_id' => $receipt->id,
                    'vehicle_id'        => $item['vehicle_id'],
                    'model_id'          => $item['model_id'],
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'discount_amount'   => $disc,
                    'amount'            => $amount,
                    'license_plate'     => $item['license_plate'] ?? null,
                    'note'              => $item['note'] ?? null,
                ]);

                $total += $amount;

                // Cập nhật xe thành đã bán
                $vehicle = Vehicle::find($item['vehicle_id']);
                if ($vehicle) {
                    $vehicle->update([
                        'status'        => 'sold',
                        'sale_price'    => $price,
                        'sale_date'     => $data['export_date'],
                        'customer_id'   => $data['customer_id'],
                        'license_plate' => $item['license_plate'] ?? $vehicle->license_plate,
                    ]);
                }
            }

            $debt = $total - $paid;
            $status = 'unpaid';
            if ($debt <= 0) {
                $status = 'paid';
                $debt   = 0;
            } elseif ($paid > 0 && $debt > 0) {
                $status = 'partial';
            }

            $receipt->update([
                'total_amount'   => $total,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);

            // Nếu có thu ngay 1 khoản, có thể tạo payment luôn
            if ($paid > 0) {
                Payment::create([
                    'export_receipt_id' => $receipt->id,
                    'payment_date'      => $data['export_date'],
                    'amount'            => $paid,
                    'method'            => 'cash',
                    'note'              => 'Thanh toán khi mua xe',
                ]);
            }
        });

        return redirect()->route('admin.export_receipts.index')
            ->with('success', 'Tạo phiếu xuất / hóa đơn bán thành công');
    }

    public function show(ExportReceipt $exportReceipt)
    {
        $exportReceipt->load(['customer', 'warehouse', 'items.vehicle.model.brand', 'payments']);
        return view('backend.export_receipts.show', compact('exportReceipt'));
    }

    public function edit(ExportReceipt $exportReceipt)
    {
        $exportReceipt->load('items');

        $warehouses = Warehouse::orderBy('name')->get();
        $customers  = Customer::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $vehicles   = Vehicle::orderBy('frame_no')->get(); // cả đã bán/lưu

        return view('backend.export_receipts.edit', compact('exportReceipt', 'warehouses', 'customers', 'models', 'vehicles'));
    }

    public function update(Request $request, ExportReceipt $exportReceipt)
    {
        $data = $request->validate([
            'export_date'   => 'required|date',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'customer_id'   => 'required|exists:customers,id',
            'export_type'   => 'nullable|string|max:50',
            'note'          => 'nullable|string',
            'paid_amount'   => 'nullable|numeric',
            'due_date'      => 'nullable|date',

            'items'                         => 'required|array|min:1',
            'items.*.id'                    => 'nullable|exists:export_receipt_items,id',
            'items.*.vehicle_id'            => 'required|exists:vehicles,id',
            'items.*.model_id'              => 'required|exists:vehicle_models,id',
            'items.*.quantity'              => 'nullable|integer|min:1',
            'items.*.unit_price'            => 'required|numeric',
            'items.*.discount_amount'       => 'nullable|numeric',
            'items.*.amount'                => 'nullable|numeric',
            'items.*.license_plate'         => 'nullable|string|max:20',
            'items.*.note'                  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $exportReceipt) {
            $paid = $data['paid_amount'] ?? $exportReceipt->paid_amount;

            $exportReceipt->update([
                'export_date'   => $data['export_date'],
                'warehouse_id'  => $data['warehouse_id'],
                'customer_id'   => $data['customer_id'],
                'export_type'   => $data['export_type'] ?? $exportReceipt->export_type,
                'note'          => $data['note'] ?? null,
                'due_date'      => $data['due_date'] ?? null,
            ]);

            $keepIds = [];
            $total   = 0;

            foreach ($data['items'] as $item) {
                $qty     = $item['quantity']        ?? 1;
                $price   = $item['unit_price'];
                $disc    = $item['discount_amount'] ?? 0;
                $amount  = $item['amount']          ?? ($qty * $price - $disc);

                if (!empty($item['id'])) {
                    $detail = ExportReceiptItem::where('export_receipt_id', $exportReceipt->id)
                        ->where('id', $item['id'])
                        ->first();

                    if ($detail) {
                        $detail->update([
                            'vehicle_id'      => $item['vehicle_id'],
                            'model_id'        => $item['model_id'],
                            'quantity'        => $qty,
                            'unit_price'      => $price,
                            'discount_amount' => $disc,
                            'amount'          => $amount,
                            'license_plate'   => $item['license_plate'] ?? null,
                            'note'            => $item['note'] ?? null,
                        ]);
                        $keepIds[] = $detail->id;
                    }
                } else {
                    $detail = ExportReceiptItem::create([
                        'export_receipt_id' => $exportReceipt->id,
                        'vehicle_id'        => $item['vehicle_id'],
                        'model_id'          => $item['model_id'],
                        'quantity'          => $qty,
                        'unit_price'        => $price,
                        'discount_amount'   => $disc,
                        'amount'            => $amount,
                        'license_plate'     => $item['license_plate'] ?? null,
                        'note'              => $item['note'] ?? null,
                    ]);
                    $keepIds[] = $detail->id;
                }

                $total += $amount;

                $vehicle = Vehicle::find($item['vehicle_id']);
                if ($vehicle) {
                    $vehicle->update([
                        'status'        => 'sold',
                        'sale_price'    => $price,
                        'sale_date'     => $data['export_date'],
                        'customer_id'   => $data['customer_id'],
                        'license_plate' => $item['license_plate'] ?? $vehicle->license_plate,
                    ]);
                }
            }

            ExportReceiptItem::where('export_receipt_id', $exportReceipt->id)
                ->whereNotIn('id', $keepIds)
                ->delete();

            $debt = $total - $paid;
            $status = 'unpaid';
            if ($debt <= 0) {
                $status = 'paid';
                $debt   = 0;
            } elseif ($paid > 0 && $debt > 0) {
                $status = 'partial';
            }

            $exportReceipt->update([
                'total_amount'   => $total,
                'paid_amount'    => $paid,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);
        });

        return redirect()->route('admin.export_receipts.index')
            ->with('success', 'Cập nhật phiếu xuất / hóa đơn thành công');
    }

    public function destroy(ExportReceipt $exportReceipt)
    {
        $exportReceipt->delete();

        return redirect()->route('admin.export_receipts.index')
            ->with('success', 'Xóa phiếu xuất / hóa đơn thành công');
    }
}
