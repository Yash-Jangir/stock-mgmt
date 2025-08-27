<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
        $perPage = 10;
        $colors = Color::where('user_id', auth()->id());

        $colors = $this->applyFilters($colors);

        $colors = $colors->paginate($perPage);

        return view('admin.colors.index', compact('colors', 'perPage'));
    }

    private function applyFilters($colors)
    {
        if (request('code')) {
            $colors = $colors->where('code', 'like', '%' . request('code') . '%');
        }

        if (request('name')) {
            $colors = $colors->where('name', 'like', '%' . request('name') . '%');
        }

        if (request('is_active') != '') {
            $colors = $colors->where('is_active', request('is_active'));
        }

        if (request('order_by')) {
            $colors = $colors->orderBy(request('order_by'), request('order', 'asc'));
        }

        $colors = $colors->latest('updated_at');

        return $colors;
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(StoreColorRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['is_active'] = (int) (bool) @$data['is_active'];

        Color::create($data);

        session()->flash('success', 'Color created successfully');

        return to_route('admin.colors.index');
    }

    public function show(Color $color)
    {
        abort_if($color->user_id !== auth()->id(), 403);

        return to_route('admin.colors.edit', $color);
    }

    public function edit(Color $color)
    {
        abort_if($color->user_id !== auth()->id(), 403);

        return view('admin.colors.edit', compact('color'));
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        abort_if($color->user_id !== auth()->id(), 403);

        $data = $request->validated();

        $data['is_active'] = (int) (bool) @$data['is_active'];

        $color->update($data);

        session()->flash('success', 'Color updated successfully');

        return to_route('admin.colors.index');
    }

    public function destroy(Color $color)
    {
        abort_if($color->user_id !== auth()->id(), 403);

        $color->delete();

        session()->flash('success', 'Color deleted successfully');

        return redirect()->back();
    }
}
