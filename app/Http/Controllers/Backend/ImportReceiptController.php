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

class ImportReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = ImportReceipt::with(['supplier', 'warehouse']);

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($from = $request->get('from')) {
            $query->whereDate('import_date', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('import_date', '<=', $to);
        }

        $receipts = $query->orderByDesc('import_date')->orderByDesc('id')->paginate(20);

        return view('backend.import_receipts.index', compact('receipts'));
    }

    public function create()
    {
        $suppliers  = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();

        return view('backend.import_receipts.create', compact('suppliers', 'warehouses', 'models'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:50|unique:import_receipts,code',
            'import_date'  => 'required|date',
            'supplier_id'  => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note'         => 'nullable|string',

            'items'                         => 'required|array|min:1',
            'items.*.vehicle_id'            => 'nullable|exists:vehicles,id',
            'items.*.model_id'              => 'required|exists:vehicle_models,id',
            'items.*.quantity'              => 'nullable|integer|min:1',
            'items.*.unit_price'            => 'nullable|numeric',
            'items.*.vat_percent'           => 'nullable|numeric',
            'items.*.amount'                => 'nullable|numeric',
            'items.*.note'                  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $userId = Auth::id();

            $receipt = ImportReceipt::create([
                'code'         => $data['code'],
                'import_date'  => $data['import_date'],
                'supplier_id'  => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'total_amount' => 0,
                'note'         => $data['note'] ?? null,
                'created_by'   => $userId,
                'approved_by'  => null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $qty        = $item['quantity']    ?? 1;
                $unitPrice  = $item['unit_price']  ?? 0;
                $amount     = $item['amount']      ?? ($qty * $unitPrice);

                ImportReceiptItem::create([
                    'import_receipt_id' => $receipt->id,
                    'vehicle_id'        => $item['vehicle_id'] ?? null,
                    'model_id'          => $item['model_id'],
                    'quantity'          => $qty,
                    'unit_price'        => $unitPrice,
                    'vat_percent'       => $item['vat_percent'] ?? null,
                    'amount'            => $amount,
                    'note'              => $item['note'] ?? null,
                ]);

                $total += $amount;
            }

            $receipt->update(['total_amount' => $total]);
        });

        return redirect()->route('admin.import_receipts.index')
            ->with('success', 'Tạo phiếu nhập thành công');
    }

    public function show(ImportReceipt $importReceipt)
    {
        $importReceipt->load(['supplier', 'warehouse', 'items.model', 'items.vehicle']);
        return view('backend.import_receipts.show', compact('importReceipt'));
    }

    public function edit(ImportReceipt $importReceipt)
    {
        $importReceipt->load('items');

        $suppliers  = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $models     = VehicleModel::with('brand')->orderBy('name')->get();

        return view('backend.import_receipts.edit', compact('importReceipt', 'suppliers', 'warehouses', 'models'));
    }

    public function update(Request $request, ImportReceipt $importReceipt)
    {
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

    public function destroy(ImportReceipt $importReceipt)
    {
        $importReceipt->delete();

        return redirect()->route('admin.import_receipts.index')
            ->with('success', 'Xóa phiếu nhập thành công');
    }
}
