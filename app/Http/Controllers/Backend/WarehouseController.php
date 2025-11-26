<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::orderBy('name')->paginate(20);
        return view('backend.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('backend.warehouses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'nullable|string|max:50',
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string|max:255',
            'note'      => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Warehouse::create($data);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Thêm kho thành công');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('backend.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'code'      => 'nullable|string|max:50',
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string|max:255',
            'note'      => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $warehouse->update($data);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Cập nhật kho thành công');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Xóa kho thành công');
    }
}
