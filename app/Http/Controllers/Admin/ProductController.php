<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Size;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class ProductController extends Controller
{
    public function index()
    {
        $perPage = 10;

        $categories = Category::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $sizes      = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $colors     = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $genders    = Gender::cases();
        $ageGroups  = AgeGroup::cases();

        $products = Product::where('user_id', auth()->id())->with('skus');

        $products = $this->applyFilters($products);

        if (request('export') == 'barcode') {
            return $this->exportBarcodes($products->whereIn('id', explode(',', request('ids')))->get());
        }

        $products = $products->paginate($perPage);

        return view('admin.products.index', compact('products', 'perPage', 'categories', 'sizes', 'colors', 'genders', 'ageGroups'));
    }

    private function applyFilters($products)
    {
        if (request('code')) {
            $products = $products->where('code', 'like', '%' . request('code') . '%');
        }

        if (request('name')) {
            $products = $products->where('name', 'like', '%' . request('name') . '%');
        }

        if (request('is_active') != '') {
            $products = $products->where('is_active', request('is_active'));
        }

        if (request('order_by')) {
            $products = $products->orderBy(request('order_by'), request('order', 'asc'));
        }

        $products = $products->latest('updated_at');

        return $products;
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $sizes      = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $colors     = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $genders    = Gender::cases();
        $ageGroups  = AgeGroup::cases();

        return view('admin.products.create', compact('categories', 'sizes', 'colors', 'genders', 'ageGroups'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $productData = [
            'user_id'       => auth()->id(),
            'is_active'     => (int) (bool) @$data['is_active'],
            'name'          => $data['name'],
            'code'          => $data['code'],
            'category_id'   => @$data['category_id'],
            'gender'        => @$data['gender'],
            'age_group'     => @$data['age_group'],
            'price'         => @$data['price'],
            'description'   => @$data['description'],
        ];

        $product = Product::create($productData);

        foreach (@$data['color_id'] as $k => $color_id) {
            $product->skus()->create([
                'user_id'     => auth()->id(),
                'color_id'    => $color_id,
                'size_id'     => $data['size_id'][$k],
                'price'       => @$data['sku_price'][$k] ?? $product->price,
                'description' => @$data['sku_description'][$k],
                'is_active'   => (int) (bool) @$data['sku_is_active'][$k],
            ]);
        }

        // Media
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $product->addMedia($file)->toMediaCollection('images');
                } catch (\Exception $e) {}
            }
        }

        session()->flash('success', 'Product created successfully');

        return to_route('admin.products.index');
    }

    public function show(Product $products)
    {
        abort_if($products->user_id !== auth()->id(), 403);

        return to_route('admin.products.edit', $products);
    }

    public function edit(Product $product)
    {
        abort_if($product->user_id !== auth()->id(), 403);

        $product->load('skus');

        $categories = Category::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $sizes      = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $colors     = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank')->latest('updated_at')->get();
        $genders    = Gender::cases();
        $ageGroups  = AgeGroup::cases();

        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors', 'genders', 'ageGroups'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        abort_if($product->user_id !== auth()->id(), 403);

        $data = $request->validated();

        $productData = [
            'is_active'     => (int) (bool) @$data['is_active'],
            'name'          => $data['name'],
            'code'          => $data['code'],
            'category_id'   => @$data['category_id'],
            'gender'        => @$data['gender'],
            'age_group'     => @$data['age_group'],
            'price'         => @$data['price'],
            'description'   => @$data['description'],
        ];

        $product->update($productData);
        $product->refresh();

        $found = [];
        foreach (@$data['color_id'] as $k => $color_id) {
            if ($id = @$data['sku_id'][$k]) {
                $found[] = $id;
                Sku::where('id', $id)
                    ->where('product_id', $product->id)
                    ->where('user_id', auth()->id())
                    ->update([
                        'color_id'    => $color_id,
                        'size_id'     => $data['size_id'][$k],
                        'price'       => @$data['sku_price'][$k] ?? $product->price,
                        'description' => @$data['sku_description'][$k],
                        'is_active'   => (int) (bool) @$data['sku_is_active'][$k],
                    ]);

            } else {
                $sku = $product->skus()->create([
                    'user_id'     => auth()->id(),
                    'color_id'    => $color_id,
                    'size_id'     => $data['size_id'][$k],
                    'price'       => @$data['sku_price'][$k] ?? $product->price,
                    'description' => @$data['sku_description'][$k],
                    'is_active'   => (int) (bool) @$data['sku_is_active'][$k],
                ]);
                $found[] = $sku->id;
            }
        }

        Sku::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->whereNotIn('id', $found)
            ->delete();

        // Media
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                try {
                    $product->addMedia($file)->toMediaCollection('images');

                } catch (\Exception $e) {}
            }
        }

        if ($request->filled('deleted_images_ids')) {
            foreach ($request->deleted_images_ids as $mediaId) {
                $mediaItem = $product->media()->where('id', $mediaId)->first();

                if ($mediaItem) {
                    $mediaItem->delete();
                }
            }
        }

        session()->flash('success', 'Product updated successfully');

        return to_route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        abort_if($product->user_id !== auth()->id(), 403);

        $product->skus()->delete();
        $product->delete();

        session()->flash('success', 'Product deleted successfully');

        return redirect()->back();
    }

    private function exportBarcodes($products)
    {
        $products->load('skus');

        $pdf = Pdf::loadView('admin.products.barcode', compact('products'))
                ->setPaper('a4', 'portrait')
                ->setOption('margin-left', '20')
                ->setOption('margin-right', '20')
                ->setOption('margin-top', '12')
                ->setOption('margin-bottom', '12');

        $pdfName = 'barcodes_' . date('YmdHis') . '.pdf';

        return $pdf->download($pdfName);
    }
}
