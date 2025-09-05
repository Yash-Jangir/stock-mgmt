<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    StockTransaction,
    Color,
    Size,
};

class SellController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $sells = StockTransaction::with('model')->where('type', 'out');

        $sells = $this->applyFilters($sells);

        $sells = $sells->paginate($perPage);

        $colors = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();
        $sizes  = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();

        return view('admin.sells.index', compact('sells', 'perPage', 'colors', 'sizes'));
    }

    private function applyFilters($sells)
    {
        if (request('product_name')) {
            $pids = Product::with('skus')->where('user_id', auth()->id())->where('name', 'like', '%' . request('product_name') . '%')->get();
            $sids = $pids->map(fn ($p) => $p->skus->pluck('id'))->flatten(1)->toArray();
            $pids = $pids->pluck('id')->toArray();

            $sells = $sells->whereHas('model', function ($q) use ($pids, $sids) {
                $q->where(function ($q) use ($pids) {
                    $q->where('model_type', Product::class)->whereIn('model_id', $pids);
                })
                ->orWhere(function ($q) use ($sids) {
                    $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
                });
            });
        }

        if (request('product_code')) {
            $pids = Product::with('skus')->where('user_id', auth()->id())->where('name', 'like', '%' . request('product_name') . '%')->get();
            $sids = $pids->map(fn ($p) => $p->skus->pluck('id'))->flatten(1)->toArray();
            $pids = $pids->pluck('id')->toArray();

            $sells = $sells->whereHas('model', function ($q) use ($pids, $sids) {
                $q->where(function ($q) use ($pids) {
                    $q->where('model_type', Product::class)->whereIn('model_id', $pids);
                })
                ->orWhere(function ($q) use ($sids) {
                    $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
                });
            });
        }

        if (request('color_id')) {
            $sids = Sku::where('user_id', auth()->id())->where('color_id', request('color_id'))->pluck('id')->toArray();
            $sells = $sells->whereHas('model', function ($q) use ($sids) {
                $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
            });
        }

        if (request('size_id')) {
            $sids = Sku::where('user_id', auth()->id())->where('size_id', request('size_id'))->pluck('id')->toArray();
            $sells = $sells->whereHas('model', function ($q) use ($sids) {
                $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
            });
        }

        if (request('order_by') && request('order')) {
            $sells = $sells->orderBy(request('order_by'), request('order'));
        }

        $sells = $sells->latest('updated_at');

        return $sells;
    }
}
