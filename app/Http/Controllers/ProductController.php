<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\ProductRating;
use App\Models\OrderDetails;
use App\Models\ProductCategory;
use App\Models\ProductImage;
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
        return response()->json([
            'products' => Product::with('product_rating', 'product_category', 'product_image')->get(),
            'categories' => ProductCategory::all()
        ]);
    }

    public function showApprovableReviews() {
        $ratings = ProductRating::with('product', 'product_image', 'user')->where('is_approved', '0')->get();
        return response()->json($ratings, 200);
    }
    public function showApprovedReviews() {
        $ratings = ProductRating::with('product', 'product_image', 'user')->where('is_approved', '1')->get();
        return response()->json($ratings, 200);        
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
        ]);

        $product->product_image()->create([
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
        $product = Product::with(['product_image', 'product_rating.user',  'product_rating' => function($query) {
            $query->where('product_ratings.is_approved', '=', 1);
        }])->whereIn('id', $product)->get();
        
        return response()->json($product, 200);
    }

    public function uploadFile(Request $request) {
        
        if( $request->hasFile('image')) {
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('products'), $name);
        }

        return response()->json($name, 201);
    }
    public function addReview(Request $request) {
        $status = ProductRating::create([
            'product_id' => $request->product,
            'user_id' => $request->user,
            'rating' => $request->rating,
            'title' => $request->title,
            'description' => $request->description,
            'is_approved' => 0
        ]);

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Review submitted!' : 'Review not submitted!'
        ]); 
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
        $oldImage = ProductImage::where('product_id', $product->id)->value('image');
        Log::info($oldImage);
        Log::info($request->get('image'));
        if(Storage::disk('products')->exists($oldImage)){
            if($oldImage !== $request->get('image')) {
                Storage::disk('products')->delete($oldImage);
            }
        }

        $status = $product->update(
            $request->only([ 'name', 'description', 'units', 'price' ])
        );
        $product->product_image()->update([
            'image' => $request->image
        ]);
        // Log::info($request->get('image'));
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

    public function approveReview($id) {
        ProductRating::find($id)->update(['is_approved' => 1]);
    }

    public function refuseReview($id) {
        ProductRating::find($id)->delete();
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
            $waitingToBeShipped=null;
            $innerJoin = OrderDetails::where('product_id', $id)->value('order_id');
            if ( is_int($innerJoin) ) {
                $waitingToBeShipped = Order::where('is_delivered', 0)
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.order_id', $innerJoin)
                ->get();
            } 

            if( isset($waitingToBeShipped) && $waitingToBeShipped->count() ) {
                $status .= '#'.$id . ', ';
            } else {
                $images = ProductImage::where('product_id', $id)->get();
                foreach($images as $image) {
                    Storage::disk('products')->delete($image->image);
                }
                Product::find($id)->delete();
                //check if this product has associated orders and order_details
                if ( is_int($innerJoin) ) {
                    $order_details = OrderDetails::where('product_id', $id)->delete();
                    $order = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                    ->where('order_details.order_id', $innerJoin)->delete();
                }
            }
        }


        if ( strlen($status) ) {
            $status = rtrim($status, ', ');
            $status = 'All products but: '.$status.' have been deleted, make sure to deliver the pending orders first.';
        }

        return response()->json([
            'status' => $status,
            'message' => !$status ? 'Products deleted' : 'Products could not be deleted.'
        ]);
    }

    public function destroy(Product $product)
    {
        $status = false;
        $id = $product->id;
        $waitingToBeShipped=null;
        $innerJoin = OrderDetails::where('product_id', $id)->value('order_id');
        if ( is_int($innerJoin) ) {
            $waitingToBeShipped = Order::where('is_delivered', 0)
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.order_id', $innerJoin)
            ->get();
        } 

        if( isset($waitingToBeShipped) && $waitingToBeShipped->count() ) {
            $status = 'Product could not be deleted.';
        } else {
            $images = ProductImage::where('product_id', $id)->get();
            foreach($images as $image) {
                Storage::disk('products')->delete($image->image);
            }
            Product::find($id)->delete();
            //check if this product has associated orders and order_details
            if ( is_int($innerJoin) ) {
                $order_details = OrderDetails::where('product_id', $id)->delete();
                $order = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.order_id', $innerJoin)->delete();
            }
        }

        if ( strlen($status) ) {
            $status = rtrim($status, ', ');
            $status = 'Make sure to deliver the pending orders first.';
        }

        return response()->json([
            'status' => $status,
            'message' => $status ? $status : 'Product has been deleted successfully.'
        ]);

    }
}
