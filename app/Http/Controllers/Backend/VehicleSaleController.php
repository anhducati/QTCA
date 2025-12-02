<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\VehicleSale;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleSaleController extends Controller
{
    protected string $moduleKey = 'vehicle_sales';

    /**
     * Check quyền module (bán lẻ xe)
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
     * FORM BÁN LẺ
     */
    public function create()
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        // Xe còn trong kho (status = 0 hoặc 'in_stock')
        $vehicles = Vehicle::with(['model.brand', 'color', 'warehouse'])
            ->where(function ($q) {
                $q->where('status', 0)
                  ->orWhere('status', 'in_stock');
            })
            ->orderBy('warehouse_id')
            ->orderBy('frame_no')
            ->get();

        // Có thể không cần khách ở form create (vì tìm theo SĐT), nhưng
        // nếu muốn dropdown khách quen thì load ra:
        $customers = Customer::orderBy('name')->limit(100)->get();

        return view('backend.vehicle_sales.create', compact('vehicles', 'customers'));
    }

    /**
     * LƯU BÁN LẺ
     */
    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) {
            return $resp;
        }

        // Validate
        $data = $request->validate([
            'sale_date'     => 'required|date',
            'vehicle_id'    => 'required|exists:vehicles,id',

            'sale_price'    => 'required|string',  // tiền dạng "30.000.000"
            'discount'      => 'nullable|string',  // "0" hoặc "500.000"
            'paid_amount'   => 'nullable|string',
            'payment_method'=> 'nullable|string|max:50',
            'sale_note'     => 'nullable|string',

            'license_plate' => 'nullable|string|max:20',

            // Khách hàng
            'customer_phone'=> 'required|string|max:20',
            'customer_name' => 'required|string|max:191',
            'customer_address' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',

        ], [
            'sale_date.required'      => 'Vui lòng chọn ngày bán.',
            'vehicle_id.required'     => 'Vui lòng chọn xe cần bán.',
            'sale_price.required'     => 'Vui lòng nhập giá bán.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại khách hàng.',
            'customer_name.required'  => 'Vui lòng nhập tên khách hàng.',
        ]);

        DB::transaction(function () use (&$sale, $data) {

            // Parse tiền: "30.000.000" -> 30000000
            $salePrice = (int) preg_replace('/\D/', '', $data['sale_price'] ?? '0');
            $discount  = (int) preg_replace('/\D/', '', $data['discount'] ?? '0');
            $paid      = (int) preg_replace('/\D/', '', $data['paid_amount'] ?? '0');

            if ($salePrice < 0) $salePrice = 0;
            if ($discount < 0)  $discount  = 0;
            if ($paid < 0)      $paid      = 0;

            $amount = max($salePrice - $discount, 0);
            $debt   = max($amount - $paid, 0);

            // Tìm hoặc tạo mới customer theo SĐT
            $customer = Customer::where('phone', $data['customer_phone'])->first();

            if (!$customer) {
                // Khách mới
                $customer = Customer::create([
                    'name'    => $data['customer_name'],
                    'phone'   => $data['customer_phone'],
                    'address' => $data['customer_address'] ?? null,
                    'note'    => $data['customer_note'] ?? null,
                ]);
            } else {
                // Khách cũ: nếu đã cho phép sửa thông tin thì cập nhật
                // Có thể thêm flag "edit_customer = 1" từ form nếu anh muốn kiểm soát
                $customer->update([
                    'name'    => $data['customer_name'],
                    'address' => $data['customer_address'] ?? $customer->address,
                    'note'    => $data['customer_note'] ?? $customer->note,
                ]);
            }

            // Lấy xe
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);

            // Sinh mã HĐ bán lẻ: HDBL_xxx
            $lastSale = VehicleSale::where('code', 'LIKE', 'HDBL_%')
                ->orderByRaw("CAST(SUBSTRING(code, 6) AS UNSIGNED) DESC")
                ->first();

            if ($lastSale) {
                $lastNumber = (int) str_replace('HDBL_', '', $lastSale->code);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $code = 'HDBL_' . $nextNumber;

            // Tạo VehicleSale
            $sale = VehicleSale::create([
                'code'          => $code,
                'sale_date'     => $data['sale_date'],
                'vehicle_id'    => $vehicle->id,
                'customer_id'   => $customer->id,

                'sale_price'    => $salePrice,
                'discount'      => $discount,
                'amount'        => $amount,

                'paid_amount'   => $paid,
                'debt_amount'   => $debt,
                'payment_status'=> $amount == 0 ? 'paid' : ($debt > 0 ? 'partial' : 'paid'),

                'payment_method'=> $data['payment_method'] ?? null,
                'note'          => $data['sale_note'] ?? null,

                'created_by'    => auth()->id(),
            ]);

            // Tạo payment nếu có số tiền thanh toán
            if ($paid > 0) {
                Payment::create([
                    'vehicle_sale_id' => $sale->id,
                    'amount'          => $paid,
                    'method'          => $data['payment_method'] ?? 'cash',
                    'paid_at'         => $data['sale_date'],
                    'note'            => 'Thanh toán khi mua xe ' . $sale->code,
                    'created_by'      => auth()->id(),
                ]);
            }

            // Cập nhật xe: đã bán, giá, ngày bán, biển số (nếu có)
            $vehicle->status     = 1; // hoặc 'sold'
            $vehicle->sale_price = $salePrice;
            $vehicle->sale_date  = $data['sale_date'];

            if (!empty($data['license_plate'])) {
                $vehicle->license_plate = $data['license_plate'];
            }

            $vehicle->save();
        });

        return redirect()
            ->route('admin.vehicle_sales.show', $sale->id)
            ->with('msg-success', 'Đã tạo hóa đơn bán lẻ: ' . $sale->code);
    }

    /**
     * XEM HÓA ĐƠN BÁN LẺ (dùng luôn làm màn hình in)
     */
    public function show(VehicleSale $vehicleSale)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $vehicleSale->load([
            'vehicle.model.brand',
            'vehicle.color',
            'customer',
            'payments',
            'createdBy',
        ]);

        return view('backend.vehicle_sales.show', [
            'sale' => $vehicleSale,
        ]);
    }

    /**
     * API: Tìm xe theo số khung (VIN)
     * GET /admin/vehicle-sales/find-vehicle?frame_no=XXX
     */
    public function findVehicle(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $frameNo = trim($request->get('frame_no', ''));

        if ($frameNo === '') {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập số khung'], 400);
        }

        $vehicle = Vehicle::with(['model.brand', 'color', 'warehouse'])
            ->where('frame_no', 'like', '%' . $frameNo . '%')
            ->where(function ($q) {
                $q->where('status', 0)
                  ->orWhere('status', 'in_stock');
            })
            ->orderBy('frame_no')
            ->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy xe phù hợp hoặc xe đã bán.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'           => $vehicle->id,
                'frame_no'     => $vehicle->frame_no,
                'engine_no'    => $vehicle->engine_no,
                'model_name'   => optional($vehicle->model)->name,
                'brand_name'   => optional(optional($vehicle->model)->brand)->name,
                'color_name'   => optional($vehicle->color)->name,
                'warehouse'    => optional($vehicle->warehouse)->name,
                'purchase_price' => $vehicle->purchase_price ?? 0,
                'suggest_price'  => $vehicle->sale_price ?? 0,
                'license_plate'=> $vehicle->license_plate,
            ]
        ]);
    }

    /**
     * API: Tìm khách theo SĐT
     * GET /admin/vehicle-sales/find-customer?phone=...
     */
    public function findCustomer(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $phone = trim($request->get('phone', ''));

        if ($phone === '') {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập số điện thoại'], 400);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Khách mới, chưa có trong hệ thống.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'      => $customer->id,
                'name'    => $customer->name,
                'phone'   => $customer->phone,
                'address' => $customer->address,
                'note'    => $customer->note,
            ]
        ]);
    }
}
