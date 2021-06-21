<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\ProductRating;
use App\Models\OrderDetails;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Product::with('product_rating', 'product_category')->get(), 200);
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
        $product = Product::create([
            'category_id' => $request->category,
            'name' => $request->name,
            'description' => $request->description,
            'units' => $request->units,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        return response()->json([
            'status' => (bool) $product,
            'data' => $product,
            'message' => $product ? 'Product created' : 'Product not created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json($product, 200);
        Log::info($product);
    }

    public function uploadFile(Request $request) {
        
        if( $request->hasFile('image')) {
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $name);
        }

        return response()->json($name, 201);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $oldImage = Product::find($product->id);

        if(Storage::disk('images')->exists($oldImage->image)){
            if($oldImage->image != $request->get('image')) {
                Storage::disk('images')->delete($oldImage->image);
            }
        }

        $status = $product->update(
            $request->only([ 'name', 'description', 'units', 'price', 'image' ])
        );
        Log::info($request->get('image'));
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product updated!' : 'Product not updated!'
        ]); 
    }

    public function updateUnits(Request $request, Product $product) {
        $product->units = $product->units + $request->get('units');
        $status = $product->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Units added' : 'Units not added'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroyMany($ids) {
        $status = false;
        $ids = explode(",", $ids);
        foreach ($ids as $id) {
            $innerJoin = OrderDetails::where('product_id', $id)->pluck('order_id');
            if ($innerJoin->count()) {
                $innerJoin = preg_replace("/[^A-Za-z0-9\-]/", '', $innerJoin);
                $waitingToBeShipped = Order::where('is_delivered', 0)
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.order_id', $innerJoin)
                ->get();
            } 

            if($waitingToBeShipped->count()) {
                $status .= '#'.$id . ', ';
            } else {
                $product = Product::find($id)->delete();
                $order_details = OrderDetails::where('product_id', $id)->delete();
                $order = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.order_id', $innerJoin)->delete();
            }
            $waitingToBeShipped = '';
            $innerJoin = '';
        }


        if ( strlen($status) ) {
            $status = rtrim($status, ', ');
            $status = 'All products but: '.$status.' have been deleted, make sure to deliver the pending orders first.';
        }
        Log::info($status);
        return response()->json([
            'status' => $status,
            'message' => !$status ? 'Products deleted' : $status ? : 'Products could not be deleted.'
        ]);
    }

    public function destroy(Product $product)
    {
        $status = false;

        $waitingToBeShipped = Order::where('product_id', $product->id)
                                   ->where('is_delivered', 0)
                                   ->get();

        if(!$waitingToBeShipped->count()) {
            $status = $product->delete();
            $product->orders()->delete();
            Storage::disk('images')->delete($product->image);
        }

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product deleted' : 'Make sure to handle the open orders from that product first'
        ]);

    }
}
