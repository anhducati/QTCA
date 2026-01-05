<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\VehicleSale;
use App\Models\VehicleSalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InventoryLogService;

class VehicleSaleController extends Controller
{
    protected string $moduleKey = 'vehicle_sales';

    // ======================= CHECK QUYỀN MODULE ==========================
    protected function authorizeModule(string $action)
    {
        $user = Auth::user();
        if (!$user || !$user->canModule($this->moduleKey, $action)) {
            return redirect()->back()->with('msg-error', 'Bạn không có quyền.');
        }
        return null;
    }

    // ======================= DANH SÁCH ==========================
    public function index(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $query = VehicleSale::with(['vehicle.model.brand', 'customer']);

        if ($request->code)
            $query->where('code', 'like', "%{$request->code}%");

        if ($request->phone) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('phone', 'like', "%{$request->phone}%");
            });
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);

        return view('backend.vehicle_sales.index', compact('sales'));
    }

    // ======================= FORM TẠO MỚI ==========================
    public function create()
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $vehicles = Vehicle::with(['model.brand','color','warehouse'])
            ->whereIn('status', [0, 'in_stock'])
            ->orderBy('frame_no')
            ->get();

        $customers = Customer::orderBy('name')->limit(100)->get();

        return view('backend.vehicle_sales.create', compact('vehicles','customers'));
    }

    // ======================= LƯU HÓA ĐƠN BÁN LẺ ==========================
    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $data = $request->validate([
            'sale_date'      => 'required|date',
            'vehicle_id'     => 'required|exists:vehicles,id',
            'customer_id'    => 'required|exists:customers,id',
            'sale_price'     => 'required',
            'paid_amount'    => 'nullable',
            'payment_method' => 'required|string|max:50',
            'note'           => 'nullable|string',
            'vehicle_note'   => 'nullable|string',
        ]);

        $sale = null;
        $vehicle = null;

        DB::transaction(function () use (&$sale, &$vehicle, $data) {

            $salePrice = (int) preg_replace('/\D/', '', $data['sale_price']);
            $paid      = (int) preg_replace('/\D/', '', $data['paid_amount'] ?? 0);

            $amount = $salePrice;
            $debt   = max($amount - $paid, 0);
            $status = $paid <= 0 ? 'unpaid' : ($debt > 0 ? 'partial' : 'paid');

            $vehicle = Vehicle::findOrFail($data['vehicle_id']);

            // SINH MÃ HDBL_x
            $last = VehicleSale::orderBy('id', 'desc')->first();
            $code = 'HDBL_' . (($last ? $last->id : 0) + 1);

            // Tạo hóa đơn
            $sale = VehicleSale::create([
                'code'           => $code,
                'sale_date'      => $data['sale_date'],
                'vehicle_id'     => $vehicle->id,
                'customer_id'    => $data['customer_id'],
                'sale_price'     => $salePrice,
                'amount'         => $amount,
                'paid_amount'    => $paid,
                'debt_amount'    => $debt,
                'payment_status' => $status,
                'payment_method' => $data['payment_method'],
                'note'           => $data['note'],
                'vehicle_note'   => $data['vehicle_note'],
                'created_by'     => auth()->id(),
            ]);

            // Lưu thanh toán đầu tiên
            if ($paid > 0) {
                VehicleSalePayment::create([
                    'vehicle_sale_id' => $sale->id,
                    'amount'          => $paid,
                    'method'          => $data['payment_method'],
                    'payment_date'    => $data['sale_date'],
                    'note'            => "Thanh toán lần đầu HĐ {$sale->code}",
                    'created_by'      => auth()->id(),
                ]);
            }

            // Cập nhật xe
            $vehicle->status      = 'sold';
            $vehicle->sale_price  = $salePrice;
            $vehicle->sale_date   = $data['sale_date'];
            $vehicle->customer_id = $data['customer_id'];
            $vehicle->save();
        });

        // 🔥 GHI NHẬT KÝ TỒN KHO — 100% chạy được
        InventoryLogService::logRetailSale($vehicle, $sale);

        return redirect()
            ->route('admin.vehicle_sales.show', $sale->id)
            ->with('msg-success', "Đã tạo hóa đơn {$sale->code}");
    }

    // ======================= XEM CHI TIẾT ==========================
    public function show(VehicleSale $sale)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $sale->load(['vehicle.model.brand','vehicle.color','customer','payments']);

        return view('backend.vehicle_sales.show', compact('sale'));
    }

    // ======================= FORM THU NỢ ==========================
    public function createPayment(VehicleSale $sale)
    {
        if ($resp = $this->authorizeModule('update')) return $resp;

        $sale->load(['payments','vehicle','customer']);

        return view('backend.vehicle_sales.payment_form', compact('sale'));
    }

    // ======================= LƯU THU NỢ ==========================
    public function storePayment(Request $request, VehicleSale $sale)
    {
        if ($resp = $this->authorizeModule('update')) return $resp;

        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount'       => 'required',
            'method'       => 'required',
            'note'         => 'nullable|string',
        ]);

        $amount = (int) preg_replace('/\D/', '', $data['amount']);

        DB::transaction(function () use ($sale, $data, $amount) {

            VehicleSalePayment::create([
                'vehicle_sale_id' => $sale->id,
                'amount'          => $amount,
                'method'          => $data['method'],
                'payment_date'    => $data['payment_date'],
                'note'            => $data['note'] ?? "Thu nợ HĐ {$sale->code}",
                'created_by'      => auth()->id(),
            ]);

            $totalPaid = $sale->payments()->sum('amount');
            $debt      = max($sale->amount - $totalPaid, 0);

            $status = $debt <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');

            $sale->update([
                'paid_amount'    => $totalPaid,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);
        });

        return redirect()
            ->route('admin.vehicle_sales.show', $sale->id)
            ->with('msg-success', 'Đã cập nhật thu nợ.');
    }

    // ======================= API TÌM XE ==========================
    public function findVehicle(Request $request)
    {
        $frame = trim($request->frame_no);

        $vehicle = Vehicle::with('model.brand','color','warehouse')
            ->where('frame_no','like',"%$frame%")
            ->whereIn('status',[0,'in_stock'])
            ->first();

        return $vehicle
            ? response()->json(['success'=>true,'vehicle'=>$vehicle])
            : response()->json(['success'=>false],404);
    }

    // ======================= API TÌM KHÁCH ==========================
    public function findCustomer(Request $request)
    {
        $customer = Customer::where('phone',$request->phone)->first();

        return $customer
            ? response()->json(['success'=>true,'customer'=>$customer])
            : response()->json(['success'=>false],404);
    }

    // ======================= CẬP NHẬT BIỂN SỐ ==========================
    public function updatePlate(Request $request)
    {
        $data = $request->validate([
            'sale_id'       => 'required|exists:vehicle_sales,id',
            'license_plate' => 'required|string|max:20',
        ]);

        $sale = VehicleSale::with('vehicle')->findOrFail($data['sale_id']);
        $sale->vehicle->license_plate = strtoupper($data['license_plate']);
        $sale->vehicle->save();

        return back()->with('msg-success','Đã cập nhật biển số.');
    }

    // ======================= IN HỢP ĐỒNG ==========================
    public function print(VehicleSale $sale)
    {
        $sale->load('vehicle.model.brand','customer');

        return Pdf::loadView('backend.vehicle_sales.print', compact('sale'))
            ->setPaper('A5')
            ->stream("HopDong_{$sale->code}.pdf");
    }
}
