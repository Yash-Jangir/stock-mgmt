<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Models\Size;

class SizeController extends Controller
{
    public function index()
    {
        $perPage = 10;
        $sizes   = Size::where('user_id', auth()->id());

        $sizes = $this->applyFilters($sizes);
        
        $sizes = $sizes->paginate($perPage);

        return view('admin.sizes.index', compact('sizes', 'perPage'));
    }

    private function applyFilters($sizes)
    {
        if (request('code')) {
            $sizes = $sizes->where('code', 'like', '%' . request('code') . '%');
        }

        if (request('name')) {
            $sizes = $sizes->where('name', 'like', '%' . request('name') . '%');
        }

        if (request('is_active') != '') {
            $sizes = $sizes->where('is_active', request('is_active'));
        }

        if (request('order_by')) {
            $sizes = $sizes->orderBy(request('order_by'), request('order', 'asc'));
        }

        $sizes = $sizes->latest('updated_at');
        
        return $sizes;
    }
    
    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(StoreSizeRequest $request)
    {
        $data = $request->validated();

        $data['user_id']   = auth()->id();
        $data['is_active'] = (int) (bool) @$data['is_active'];

        Size::create($data);

        session()->flash('success', 'Size created successfully');

        return to_route('admin.sizes.index');
    }

    public function show(Size $size)
    {
        abort_if($size->user_id !== auth()->id(), 403);

        return to_route('admin.sizes.edit', $size);
    }
    
    public function edit(Size $size)
    {
        abort_if($size->user_id !== auth()->id(), 403);

        return view('admin.sizes.edit', compact('size'));
    }

    public function update(UpdateSizeRequest $request, Size $size)
    {
        abort_if($size->user_id !== auth()->id(), 403);

        $data = $request->validated();

        $data['is_active'] = (int) (bool) @$data['is_active'];

        $size->update($data);

        session()->flash('success', 'Size updated successfully');

        return to_route('admin.sizes.index');
    }

    public function destroy(Size $size)
    {
        abort_if($size->user_id !== auth()->id(), 403);

        $size->delete();

        session()->flash('success', 'Size deleted successfully');

        return redirect()->back();
    }
}
