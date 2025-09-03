<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Sku;
use App\Models\Stock;
use App\Models\Color;
use App\Models\Size;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $stocks = Stock::with('model');

        $stocks = $this->applyFilters($stocks);

        $stocks = $stocks->paginate($perPage);

        $colors = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();
        $sizes  = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();

        return view('admin.stocks.index', compact('stocks', 'perPage', 'colors', 'sizes'));
    }

    private function applyFilters($stocks)
    {
        if (request('product_name')) {
            $pids = Product::with('skus')->where('user_id', auth()->id())->where('name', 'like', '%' . request('product_name') . '%')->get();
            $sids = $pids->map(fn ($p) => $p->skus->pluck('id'))->flatten(1)->toArray();
            $pids = $pids->pluck('id')->toArray();

            $stocks = $stocks->whereHas('model', function ($q) use ($pids, $sids) {
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

            $stocks = $stocks->whereHas('model', function ($q) use ($pids, $sids) {
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
            $stocks = $stocks->whereHas('model', function ($q) use ($sids) {
                $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
            });
        }

        if (request('size_id')) {
            $sids = Sku::where('user_id', auth()->id())->where('size_id', request('size_id'))->pluck('id')->toArray();
            $stocks = $stocks->whereHas('model', function ($q) use ($sids) {
                $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
            });
        }

        if (request('type')) {
            $stocks = $stocks->where('type', request('type'));
        }

        if (request('order_by') && request('order')) {
            $stocks = $stocks->orderBy(request('order_by'), request('order'));
        }

        $stocks = $stocks->latest('updated_at');

        return $stocks;
    }

    public function create()
    {
        $products = Product::with(['stock', 'skus', 'skus.stock', 'skus.color', 'skus.size'])
                        ->where('is_active', 1)
                        ->whereHas('skus', function ($query) {
                            $query->where('is_active', 1);
                        })
                        ->latest('updated_at')
                        ->get();

        $transaction_types = TransactionType::cases();

        return view('admin.stocks.create', compact('products', 'transaction_types'));
    }

    public function store(StoreStockRequest $request)
    {
        $unsavedStocks = [];

        \DB::transaction(function() use ($request, &$unsavedStocks) {
            $multiplier = ($request->type === TransactionType::IN->value ? 1 : -1);
            
            foreach ($request->product_id as $k => $id) {
                $stockQty = $request->stock_qty[$k];
                $instance = $this->resolveModelInstance($id);

                if (!$instance || (int) $stockQty === 0) continue;

                $instance->transaction()->create([
                    'user_id'   => auth()->id(),
                    'type'      => $request->type,
                    'stock_qty' => $stockQty
                ]);
    
                $stock = $instance->stock;
                if ($multiplier == -1 && @$stock->stock_qty < $stockQty) {
                    $unsavedStocks[] = ($instance instanceof Product) ? $instance->name : "{$instance->product?->name}-[{$instance->color?->name}]-[{$instance->size?->name}]"; 
                    continue;
                }
    
                $instance->stock()->updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'model_type' => get_class($instance),
                        'model_id'   => $instance->id
                    ], [
                        'stock_qty' => (@$stock->stock_qty) + ($stockQty * $multiplier)
                    ]
                );
            }
        });

        session()->flash('success', 'Stock updated successfully');

        if ($unsavedStocks) {
            session()->flash('error', $unsavedStocks);
        }

        return to_route('admin.stocks.index');
    }

    private function resolveModelInstance($id)
    {
        $ids = explode("|", $id);
        if (count($ids) !== 2) return null;

        $id1 = explode(":", $ids[0]);
        $id2 = explode(":", $ids[1]);

        if (count($id1) !== 2 && count($id2) !== 2) return null;

        if ($id1[0] === $id2[0] && $id1[0] === 'p') {
            $model = Product::class;
            $id    = $id1[1];
        } else if ($id2[0] === 's') {
            $model = Sku::class;
            $id    = $id2[1];
        }

        $instance = $model::where('user_id', auth()->id())->where('is_active', 1)->find($id);
        if ($instance && $instance instanceof Product) {
            $instance->load('skus');
            $instance = $instance->skus->count() ? null : $instance;
        }

        return $instance;
    }

    public function scan(Request $request, $type)
    {
        return view('admin.stocks.scan', compact('type'));
    }

    public function scanResult(Request $request)
    {
        
    }
}
