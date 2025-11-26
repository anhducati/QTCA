<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('name')->paginate(50);
        return view('backend.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('backend.colors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'hex_code' => 'nullable|string|max:10',
            'note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Color::create($data);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Thêm màu xe thành công');
    }

    public function edit(Color $color)
    {
        return view('backend.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'hex_code' => 'nullable|string|max:10',
            'note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $color->update($data);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Cập nhật màu xe thành công');
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return redirect()->route('admin.colors.index')
            ->with('success', 'Xóa màu xe thành công');
    }
}
