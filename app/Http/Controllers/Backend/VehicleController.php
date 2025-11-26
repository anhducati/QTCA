<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\Color;
use App\Models\Warehouse;
use App\Models\Customer;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['model.brand', 'color', 'warehouse', 'customer']);

        if ($kw = $request->get('q')) {
            $query->where(function ($q) use ($kw) {
                $q->where('frame_no', 'like', "%{$kw}%")
                  ->orWhere('engine_no', 'like', "%{$kw}%")
                  ->orWhere('license_plate', 'like', "%{$kw}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $vehicles = $query->orderByDesc('id')->paginate(20);

        return view('backend.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $colors     = Color::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $customers  = Customer::orderBy('name')->get();

        return view('backend.vehicles.create', compact('models', 'colors', 'warehouses', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model_id'       => 'required|exists:vehicle_models,id',
            'color_id'       => 'nullable|exists:colors,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'frame_no'       => 'required|string|max:100|unique:vehicles,frame_no',
            'engine_no'      => 'nullable|string|max:100',
            'year'           => 'nullable|integer',
            'battery_no'     => 'nullable|string|max:100',
            'imei'           => 'nullable|string|max:100',
            'license_plate'  => 'nullable|string|max:20',
            'registered_at'  => 'nullable|date',
            'status'         => 'nullable|string|max:50',
            'import_price'   => 'nullable|numeric',
            'sale_price'     => 'nullable|numeric',
            'sale_date'      => 'nullable|date',
            'customer_id'    => 'nullable|exists:customers,id',
            'note'           => 'nullable|string',
        ]);

        if (empty($data['status'])) {
            $data['status'] = 'in_stock';
        }

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Thêm xe thành công');
    }

    public function edit(Vehicle $vehicle)
    {
        $models     = VehicleModel::with('brand')->orderBy('name')->get();
        $colors     = Color::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $customers  = Customer::orderBy('name')->get();

        return view('backend.vehicles.edit', compact('vehicle', 'models', 'colors', 'warehouses', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'model_id'       => 'required|exists:vehicle_models,id',
            'color_id'       => 'nullable|exists:colors,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'frame_no'       => 'required|string|max:100|unique:vehicles,frame_no,' . $vehicle->id,
            'engine_no'      => 'nullable|string|max:100',
            'year'           => 'nullable|integer',
            'battery_no'     => 'nullable|string|max:100',
            'imei'           => 'nullable|string|max:100',
            'license_plate'  => 'nullable|string|max:20',
            'registered_at'  => 'nullable|date',
            'status'         => 'nullable|string|max:50',
            'import_price'   => 'nullable|numeric',
            'sale_price'     => 'nullable|numeric',
            'sale_date'      => 'nullable|date',
            'customer_id'    => 'nullable|exists:customers,id',
            'note'           => 'nullable|string',
        ]);

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Cập nhật xe thành công');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Xóa xe thành công');
    }
}
