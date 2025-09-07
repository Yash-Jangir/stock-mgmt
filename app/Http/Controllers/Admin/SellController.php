<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Traits\SkuResolver;
use App\Http\Requests\StoreSellRequest;
use App\Models\{
    Color,
    Size,
    Product,
    Sku,
    BillingSlip,
    BillingDetail,
};

class SellController extends Controller
{
    use SkuResolver;

    public function index(Request $request)
    {
        $perPage = 10;
        $sells = BillingSlip::where('user_id', auth()->id())->where('classification', 'sell');

        $sells = $this->applyFilters($sells);

        $sells = $sells->paginate($perPage);
        $details   = BillingDetail::whereIn('billing_slip_id', $sells->pluck('id'))->groupBy('billing_slip_id')->selectRaw('billing_slip_id, count(*) as count')->get()->pluck('count', 'billing_slip_id')->toArray();

        $colors = Color::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();
        $sizes  = Size::where('user_id', auth()->id())->where('is_active', 1)->orderBy('rank', 'asc')->latest('updated_at')->get();

        return view('admin.sells.index', compact('sells', 'perPage', 'colors', 'sizes'));
    }

    private function applyFilters($sells)
    {
        if (request('customer_name')) {
            $sells = $sells->where('client_name', 'LIKE', "%" . request('customer_name') . "%");
        }

        if (request('slip_date_from')) {
            $sells = $sells->where('slip_date', '>=', date('Y-m-d', strtotime(request('slip_date_from'))));
        }
        if (request('slip_date_to')) {
            $sells = $sells->where('slip_date', '<=', date('Y-m-d', strtotime(request('slip_date_to'))));
        }

        if (request('order_by') && request('order')) {
            $sells = $sells->orderBy(request('order_by'), request('order'));
        }

        $sells = $sells->latest('updated_at');

        return $sells;
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

        $slip_no  = BillingSlip::newSlipNo('sell');

        return view('admin.sells.create', compact('products', 'slip_no'));
    }

    public function store(StoreSellRequest $request)
    {
        $unsavedStocks = [];

        try {
            \DB::transaction(function() use ($request, &$unsavedStocks) {
                $multiplier = -1;

                $bill = BillingSlip::create([
                    'user_id'           => auth()->id(),
                    'year'              => date('Y'),
                    'seq'               => BillingSlip::newSeqNo('sell'),
                    'slip_date'         => date('Y-m-d'),
                    'classification'    => 'sell',
                    'client_name'       => $request->customer_name,
                    'address'           => $request->address,
                    'gst_number'        => $request->gst_number,
                    'contact_no'        => $request->contact_no,
                    'email'             => $request->email,
                    'discount'          => $request->discount,
                    'total_price'       => $request->total_price,
                ]);
                
                foreach ($request->product_id as $k => $id) {
                    $stockQty = $request->qty[$k];
                    $instance = $this->resolveInstance($id);

                    if (!$instance) {
                        throw ValidationException::withMessages([
                            'invalid_product' => "Product Not found",
                        ]);
                    }

                    if (!$instance || (int) $stockQty === 0) continue;

                    $stock = $instance->stock;
                    if ($multiplier == -1 && @$stock->stock_qty < $stockQty) {
                        $unsavedStocks[] = ($instance instanceof Product) ? $instance->name : "{$instance->product?->name}-[{$instance->color?->name}]-[{$instance->size?->name}]"; 
                        continue;
                    }

                    // Billing Detail
                    [$product, $sku] = $instance instanceof Product ? [$instance, null] : [$instance->product, $instance];
                    $detailRecord    = BillingDetail::create([
                        'user_id'           => auth()->id(),
                        'billing_slip_id'   => $bill->id,
                        'product_id'        => $product->id,
                        'sku_id'            => @$sku->id,
                        'qty'               => $stockQty,
                        'unit_price'        => $request->unit_price[$k],
                        'price'             => $request->price[$k],
                    ]);

                    // Transaction
                    $instance->transaction()->create([
                        'user_id'        => auth()->id(),
                        'type'           => 'out',
                        'stock_qty'      => $stockQty,
                        'price'          => $request->price[$k],
                        'dis_price'      => $request->price[$k] - ($request->price[$k] * $request->discount / 100),
                        'discount'       => $request->discount,
                        'bill_id'        => $bill->id,
                        'bill_detail_id' => $detailRecord->id,
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
                }

                if ($unsavedStocks) {
                    throw ValidationException::withMessages([
                        'stock' => 'The following stocks are not enough: <br>' . implode('<br>', $unsavedStocks)
                    ]);
                }
            });

            session()->flash('success', 'Sell slip created successfully');

            return to_route('admin.sells.index');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show($id)
    {
        $billing = BillingSlip::where('user_id', auth()->id())->find($id);
        abort_if(!$billing, 403);

        $slip_no  = $billing->slip_no;

        $details  = BillingDetail::where('user_id', auth()->id())->where('billing_slip_id', $billing->id)->get();

        $products = Product::where('user_id', auth()->id())->whereIn('id', $details->pluck('product_id'))->get()->keyBy('id');
        $skus     = Sku::with(['color', 'size'])->where('user_id', auth()->id())->whereIn('id', $details->pluck('sku_id'))->get()->keyBy('id');

        return view('admin.sells.show', compact('billing', 'details', 'products', 'skus', 'slip_no'));
    }
}
