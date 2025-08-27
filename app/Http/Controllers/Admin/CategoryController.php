<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $perPage = 10;
        $categories = Category::where('user_id', auth()->id());

        $categories = $this->applyFilters($categories);

        $categories = $categories->paginate($perPage);

        return view('admin.categories.index', compact('categories', 'perPage'));
    }

    private function applyFilters($categories)
    {
        if (request('code')) {
            $categories = $categories->where('code', 'like', '%' . request('code') . '%');
        }

        if (request('name')) {
            $categories = $categories->where('name', 'like', '%' . request('name') . '%');
        }

        if (request('is_active') != '') {
            $categories = $categories->where('is_active', request('is_active'));
        }

        if (request('order_by')) {
            $categories = $categories->orderBy(request('order_by'), request('order', 'asc'));
        }

        $categories = $categories->latest('updated_at');

        return $categories;
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['is_active'] = (int) (bool) @$data['is_active'];

        Category::create($data);

        session()->flash('success', 'Category created successfully');

        return to_route('admin.categories.index');
    }

    public function show(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        return to_route('admin.categories.edit', $category);
    }

    public function edit(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        $data = $request->validated();

        $data['is_active'] = (int) (bool) @$data['is_active'];

        $category->update($data);

        session()->flash('success', 'Category updated successfully');

        return to_route('admin.categories.index');
    }

    public function destroy(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        $category->delete();

        session()->flash('success', 'Category deleted successfully');

        return redirect()->back();
    }
}
