<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Sku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\StockTransaction;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $transactions = StockTransaction::with('model');

        $transactions = $this->applyFilters($transactions);

        $transactions = $transactions->paginate($perPage);

        return view('admin.transactions.index', compact('transactions', 'perPage'));
    }

    private function applyFilters($transactions)
    {
        if (request('product_name')) {
            $pids = Product::with('skus')->where('user_id', auth()->id())->where('name', 'like', '%' . request('product_name') . '%')->get();
            $sids = $pids->map(fn ($p) => $p->skus->pluck('id'))->flatten(1)->toArray();
            $pids = $pids->pluck('id')->toArray();

            $transactions = $transactions->whereHas('model', function ($q) use ($pids, $sids) {
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

            $transactions = $transactions->whereHas('model', function ($q) use ($pids, $sids) {
                $q->where(function ($q) use ($pids) {
                    $q->where('model_type', Product::class)->whereIn('model_id', $pids);
                })
                ->orWhere(function ($q) use ($sids) {
                    $q->where('model_type', Sku::class)->whereIn('model_id', $sids);
                });
            });
        }

        if (request('type')) {
            $transactions = $transactions->where('type', request('type'));
        }

        if (request('order_by') && request('order')) {
            $transactions = $transactions->orderBy(request('order_by'), request('order'));
        }

        $transactions = $transactions->latest('updated_at');

        return $transactions;
    }
}

