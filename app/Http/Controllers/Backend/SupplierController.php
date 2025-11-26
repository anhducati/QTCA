<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(20);
        return view('backend.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('backend.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'     => 'nullable|string|max:50',
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
            'address'  => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:50',
            'note'     => 'nullable|string',
            'is_active'=> 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Thêm nhà cung cấp thành công');
    }

    public function edit(Supplier $supplier)
    {
        return view('backend.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'code'     => 'nullable|string|max:50',
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
            'address'  => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:50',
            'note'     => 'nullable|string',
            'is_active'=> 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Cập nhật nhà cung cấp thành công');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Xóa nhà cung cấp thành công');
    }
}
