<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use App\Models\Brand;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function index()
    {
        $models = VehicleModel::with('brand')
            ->orderBy('brand_id')
            ->orderBy('name')
            ->paginate(20);

        return view('backend.models.index', compact('models'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('backend.models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:50',
            'cylinder_cc' => 'nullable|integer',
            'year_default' => 'nullable|integer',
            'note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        VehicleModel::create($data);

        return redirect()->route('admin.models.index')
            ->with('success', 'Thêm dòng xe thành công');
    }

    public function edit(VehicleModel $model)
    {
        $brands = Brand::orderBy('name')->get();
        return view('backend.models.edit', compact('model', 'brands'));
    }

    public function update(Request $request, VehicleModel $model)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:50',
            'cylinder_cc' => 'nullable|integer',
            'year_default' => 'nullable|integer',
            'note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $model->update($data);

        return redirect()->route('admin.models.index')
            ->with('success', 'Cập nhật dòng xe thành công');
    }

    public function destroy(VehicleModel $model)
    {
        $model->delete();

        return redirect()->route('admin.models.index')
            ->with('success', 'Xóa dòng xe thành công');
    }
}
