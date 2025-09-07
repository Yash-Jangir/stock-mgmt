<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Traits\SkuResolver;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\{
    Color,
    Size,
    Product,
    Sku,
    BillingSlip,
    BillingDetail,
};

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class PurchaseController extends Controller
{
    use SkuResolver;

    public function index(Request $request)
    {
        $perPage = 10;
        $purchases = BillingSlip::where('user_id', auth()->id())->where('classification', 'purchase');

        if (request('export') == 'pdf') {
            return $this->exportPDF($purchases->find(request('id')));
        }

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

        $slip_no  = BillingSlip::newSlipNo('purchase');

        return view('admin.purchases.create', compact('products', 'slip_no'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $unsavedStocks = [];

        try {
            \DB::transaction(function() use ($request, &$unsavedStocks) {
                $multiplier = 1;

                $bill = BillingSlip::create([
                    'user_id'           => auth()->id(),
                    'year'              => date('Y'),
                    'seq'               => BillingSlip::newSeqNo('purchase'),
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
                        'type'           => 'in',
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
                        'stock' => 'The following stocks are not enough: ' . implode(', ', $unsavedStocks)
                    ]);
                }
            });

            session()->flash('success', 'Purchase slip created successfully');

            return to_route('admin.purchases.index');
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

        return view('admin.purchases.show', compact('billing', 'details', 'products', 'skus', 'slip_no'));
    }

    private function exportPDF($billing)
    {
        abort_if(!$billing, 404);

        $details  = BillingDetail::where('user_id', auth()->id())->where('billing_slip_id', $billing->id)->get();

        $products = Product::where('user_id', auth()->id())->whereIn('id', $details->pluck('product_id'))->get()->keyBy('id');
        $skus     = Sku::with(['color', 'size'])->where('user_id', auth()->id())->whereIn('id', $details->pluck('sku_id'))->get()->keyBy('id');

        view()->share(compact('billing', 'details', 'products', 'skus'));

        $pdfName = 'PurchaseSlip_' . $billing->slip_no . '_' . date('YmdHis') . ".pdf";
        $pdf     = Pdf::loadView('admin.purchases.htmltopdf');

        return $pdf->inline($pdfName);

        // return view('admin.purchases.htmltopdf', );
    }
}
