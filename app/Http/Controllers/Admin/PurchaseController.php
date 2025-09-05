<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\{
    Color,
    Size,
    Product,
    Sku,
    BillingSlip,
    BillingDetail,
};

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $purchases = BillingSlip::where('user_id', auth()->id())->where('classification', 'purchase');

        $purchases = $this->applyFilters($purchases);

        $purchases = $purchases->paginate($perPage);
        $details   = BillingDetail::whereIn('billing_slip_id', $purchases->pluck('id'))->groupBy('billing_slip_id')->selectRaw('billing_slip_id, count(*) as count')->get()->pluck('count', 'billing_slip_id')->toArray();

        $colors = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();
        $sizes  = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();

        return view('admin.purchases.index', compact('purchases', 'perPage', 'colors', 'sizes'));
    }

    private function applyFilters($purchases)
    {
        if (request('supplier_name')) {
            $purchases = $purchases->where('client_name', 'LIKE', "%" . request('supplier_name') . "%");
        }

        if (request('slip_date_from')) {
            $purchases = $purchases->where('slip_date', '>=', date('Y-m-d', strtotime(request('slip_date_from'))));
        }
        if (request('slip_date_to')) {
            $purchases = $purchases->where('slip_date', '<=', date('Y-m-d', strtotime(request('slip_date_to'))));
        }

        if (request('order_by') && request('order')) {
            $purchases = $purchases->orderBy(request('order_by'), request('order'));
        }

        $purchases = $purchases->latest('updated_at');

        return $purchases;
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

        $slip_no  = BillingSlip::newSlipNo();

        return view('admin.purchases.create', compact('products', 'slip_no'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $unsavedStocks = [];

        \DB::transaction(function() use ($request, &$unsavedStocks) {
            $multiplier = 1;

            $bill = BillingSlip::create([
                'user_id'           => auth()->id(),
                'year'              => date('Y'),
                'seq'               => BillingSlip::newSeqNo(),
                'slip_date'         => date('Y-m-d'),
                'classification'    => 'purchase',
                'client_name'       => $request->supplier_name,
                'address'           => $request->address,
                'gst_number'        => $request->gst_number,
                'contact_no'        => $request->contact_no,
                'email'             => $request->email,
                'discount'          => $request->discount,
                'total_price'       => $request->total_price,
            ]);
            
            foreach ($request->product_id as $k => $id) {
                $stockQty = $request->qty[$k];
                $instance = $this->resolveModelInstance($id);

                if (!$instance || (int) $stockQty === 0) continue;

                $stock = $instance->stock;
                if ($multiplier == -1 && @$stock->stock_qty < $stockQty) {
                    $unsavedStocks[] = ($instance instanceof Product) ? $instance->name : "{$instance->product?->name}-[{$instance->color?->name}]-[{$instance->size?->name}]"; 
                    continue;
                }

                // Transaction
                $instance->transaction()->create([
                    'user_id'   => auth()->id(),
                    'type'      => 'in',
                    'stock_qty' => $stockQty,
                    'price'     => $request->price[$k],
                    'dis_price' => $request->price[$k] - ($request->price[$k] * $request->discount / 100),
                    'discount'  => $request->discount,
                ]);
    
                // Stock
                $instance->stock()->updateOrCreate(
                    [
                        'user_id'    => auth()->id(),
                        'model_type' => get_class($instance),
                        'model_id'   => $instance->id
                    ], [
                        'stock_qty' => (@$stock->stock_qty) + ($stockQty * $multiplier)
                    ]
                );

                // Billing Detail
                [$product, $sku] = $instance instanceof Product ? [$instance, null] : [$instance->product, $instance];
                BillingDetail::create([
                    'user_id'           => auth()->id(),
                    'billing_slip_id'   => $bill->id,
                    'product_id'        => $product->id,
                    'sku_id'            => @$sku->id,
                    'qty'               => $stockQty,
                    'unit_price'        => $request->unit_price[$k],
                    'price'             => $request->price[$k],
                ]);
            }
        });

        session()->flash('success', 'Purchase slip created successfully');

        if ($unsavedStocks) {
            session()->flash('error', $unsavedStocks);
        }

        return to_route('admin.purchases.index');
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

    public function show($id)
    {
        $billing = BillingSlip::where('user_id', auth()->id())->find($id);
        abort_if(!$billing, 404);

        $slip_no  = $billing->slip_no;

        $details  = BillingDetail::where('user_id', auth()->id())->where('billing_slip_id', $billing->id)->get();

        $products = Product::where('user_id', auth()->id())->whereIn('id', $details->pluck('product_id'))->get()->keyBy('id');
        $skus     = Sku::with(['color', 'size'])->where('user_id', auth()->id())->whereIn('id', $details->pluck('sku_id'))->get()->keyBy('id');

        return view('admin.purchases.show', compact('billing', 'details', 'products', 'skus', 'slip_no'));
    }
}
