<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Sku;
use App\Models\Stock;
use App\Models\StockTransaction;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $stocks = Stock::with('model')->latest('updated_at')->paginate($perPage);

        // dd($stocks->all());

        return view('admin.stocks.index', compact('stocks', 'perPage'));
    }

    public function create()
    {
        $products = Product::with(['skus', 'skus.color', 'skus.size'])
                        ->where('is_active', 1)
                        ->whereHas('skus', function ($query) {
                            $query->where('is_active', 1);
                        })
                        ->latest('updated_at')
                        ->get();

        $transaction_types = TransactionType::cases();

        return view('admin.stocks.create', compact('products', 'transaction_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|string|max:255',
            'type'          => 'required|string|in:in,out',
            'stock_qty'     => 'required|numeric|min:1',
        ]);

        $instance = $this->resolveModelInstance($request->product_id);

        if (!$instance) {
            return redirect()->route('admin.stocks.create')->with('error', 'Product not found');
        }

        \DB::transaction(function()use ($instance, $request) {
            $instance->transaction()->create([
                'user_id'   => auth()->id(),
                'type'      => $request->type,
                'stock_qty' => $request->stock_qty
            ]);

            $stock = $instance->stock;

            $instance->stock()->updateOrCreate(
                [
                    'user_id'    => auth()->id(),
                    'model_type' => get_class($instance),
                    'model_id'   => $instance->id
                ], [
                    'stock_qty' => (@$stock->stock_qty) + ($request->stock_qty * ($request->type === TransactionType::IN->value ? 1 : -1))
                ]
            );
        });

        session()->flash('success', 'Stock updated successfully');

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
}
