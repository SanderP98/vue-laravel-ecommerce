<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
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
        return response()->json(Product::all(), 200);
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
        $status = Product::whereIn('id',explode(",",$ids))->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Products deleted' : 'Products not deleted'
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
