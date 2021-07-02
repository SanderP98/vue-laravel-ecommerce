<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Address;
use App\Models\Product;
use App\Models\PaymentMethod;
use Auth;
use Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function  __construct() {
        Mollie::api()->setApiKey('test_jEVj5aVaURvn3Us4Pw5fugxxhxaqEn'); // your mollie test api key
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Order::with('order_details', 'order_details.product', 'address', 'user')->get(), 200);
    }

    public function getPaymentMethods($bool) {
        if ( !$bool ) {
            return response()->json(PaymentMethod::get(), 200);
        } else {
            return response()->json(PaymentMethod::where('is_active', $bool)->get(), 200);
        }
    }
    public function deliverOrder(Order $order) {
        $order->is_delivered = true;
        $status = $order->save();

        return response()->json([
            'status' => $status,
            'data' => $order,
            'message' => $order ? 'Order delivered' : 'Order not delivered'
        ]);
    }
    public function preparePayment(Request $request)
    {   
        if ( $request ) {
            $products = $request->get('products');
            Log::info($products);
            // if (!is_object($products)) {
            //     $products = (object) $products;
            // }
            $totalPrice = number_format($request->get('totalPrice'), 2, '.', '');
            // Payment Methods can not be enabled when not being registered as a business, but it does work.
            $method = $request->get('paymentMethod');
            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => 'EUR', // Type of currency you want to send
                    'value' => $totalPrice, // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'method' => $method,
                'description' => "Beschrijving", 
                'webhookUrl' => route('webhooks.mollie'),
                'redirectUrl' => route('payment.success', ['is_singular' => $request->get('isSingleProduct') ? $request->get('isSingleProduct') : 0]),
                "metadata" => [
                    "products" => $products,
                    "user_id" => Auth::id(),
                    "user" => Auth::user(),
                    "totalPrice" => $totalPrice,
                ], 
            ]);
        
            $payment = Mollie::api()->payments()->get($payment->id);
        
            // redirect customer to Mollie checkout page
            return response()->json([
                'data' => $payment,
            ]);
        } else {
            return redirect()->to('/');           
        }
    }
    public function paymentSuccess(Request $request, $id) {
        //Hier de bestelling afronden en de status op betaald zetten.
        //$payment = Mollie::api()->payments->get($request->id);
        // if (! $request->has('id')) {
        //     return;
        // }
        // $payment = Mollie::api()->payments()->get($request->id);

        // if ( $payment->metadata->isSingleProduct == true ) {
        //     return redirect()->to('/dashboard/orders');           
        // } else {
        //     return redirect()->to('/dashboard/orders?success=1');
        // }
        // return response()->json(['data' => 'test']);
        Log::info($id);
        if ( $id == 0 ) {
            return redirect()->to('/dashboard/orders?clear_cart=1');
        } else {
            return redirect()->to('/dashboard/orders');       
        }
    }
    
    public function webhook(Request $request) {
        if (! $request->has('id')) {
            return;
        }
        $payment = Mollie::api()->payments()->get($request->id);
        
        //Log::info('Test '.$payment->metadata->order_id);

        $statusOfPayment='';

        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
            /*
             * The payment is paid and isn't refunded or charged back.
             * At this point you'd probably want to start the process of delivering the product to the customer.
             */
               $statusOfPayment='paid';
     
        } elseif ($payment->isOpen()) {
            /*
             * The payment is open.
             */
             $statusOfPayment='open';
        } elseif ($payment->isPending()) {
            /*
             * The payment is pending.
             */
             $statusOfPayment='pending';
     
        } elseif ($payment->isFailed()) {
            /*
             * The payment has failed.
             */
            $statusOfPayment='failed';
     
        } elseif ($payment->isExpired()) {
            /*
             * The payment is expired.
             */
        } elseif ($payment->isCanceled()) {
            /*
             * The payment has been canceled.
             */
     
              $statusOfPayment='expired';
     
        } elseif ($payment->hasRefunds()) {
            /*
             * The payment has been (partially) refunded.
             * The status of the payment is still "paid"
             */
     
                $statusOfPayment='partially refunded';
        } elseif ($payment->hasChargebacks()) {
            /*
             * The payment has been (partially) charged back.
             * The status of the payment is still "paid"
             */
              $statusOfPayment='partially charged back';
        }

        $order = Order::create([
            'user_id' => $payment->metadata->user_id,
            'address_id' => 1, 
            'order_status' => $statusOfPayment,
            'total_price' => $payment->metadata->totalPrice,
            'is_delivered' => 0,     
        ]);
        // Log::info($payment->metadata->products);
        // $order_details = $order->order_details()->create([
        //     'quantity' => '1',
        //     'totalPrice' => '8000',
        // ]);
        foreach($payment->metadata->products as $product) {
            // Log::info($product->id);
            
            $order_details = $order->order_details()->create([
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'total_price' => $product->price * $product->quantity,
            ]);

            $productUnits = Product::find($product->id);

            $productUnits->decrement('units', $product->quantity);

            $productUnits->save();
            
        }
        Mail::to($payment->metadata->user->email)->send(new OrderCreatedMail($order));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = Order::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'address' => $request->address,            
        ]);

        return response()->json([
            'status' => (bool) $order,
            'data' => $order,
            'message' => $order ? 'Order created' : 'Order created',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Order::with('order_details', 'order_details.product', 'address')->where('id', $id)->withTrashed()->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $status = $order->update([
            $request->only(['quantity'])
        ]);

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order updated' : 'Order not updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $status = $order->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order deleted' : 'Order not deleted'
        ]);
    }
    
    public function destroyMany($ids) {
        $status = Order::whereIn('id',explode(",",$ids))->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Orders deleted' : 'Orders not deleted'
        ]);
    }
}
