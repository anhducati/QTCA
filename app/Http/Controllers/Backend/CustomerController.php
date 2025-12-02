<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(20);

        return view('backend.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('backend.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'    => 'nullable|string|max:50',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'cccd'    => 'nullable|string|max:20',
            'dob'     => 'nullable|date',
            'gender'  => 'nullable|integer',
            'note'    => 'nullable|string',
        ]);

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Thêm khách hàng thành công');
    }

    public function edit(Customer $customer)
    {
        return view('backend.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'code'    => 'nullable|string|max:50',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'cccd'    => 'nullable|string|max:20',
            'dob'     => 'nullable|date',
            'gender'  => 'nullable|integer',
            'note'    => 'nullable|string',
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Cập nhật khách hàng thành công');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Xóa khách hàng thành công');
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        if (!$user || !$user->canModule('customers', 'read')) {
            return redirect()->back()
                ->with('msg-error', 'Bạn không có quyền xem khách hàng.');
        }

        // Load các hóa đơn bán lẻ + thông tin xe
        $customer->load([
            'vehicleSales.vehicle.model.brand',
            'vehicleSales.vehicle.color',
        ]);

        // Lấy list hóa đơn, sort mới nhất trước
        $sales = $customer->vehicleSales()
            ->orderByDesc('sale_date')
            ->get();

        return view('backend.customers.show', compact('customer', 'sales'));
    }
}
