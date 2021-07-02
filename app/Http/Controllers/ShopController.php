<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShopSettings;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;


class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ShopSettings::all(), 200);
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

    public function uploadFile(Request $request) {
        
        if( $request->hasFile('image')) {
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('shop'), $name);
        }

        return response()->json($name, 201);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ShopSettings::create(
            $request->only([ 'name', 'image', 'email', 'tel_nr', 'mollie_api_key' ])
        );
        foreach ($request->paymentOptions as $paymentOption) {
            $paymentOption = $paymentOption['name'];
            PaymentMethod::where('is_active', 0)
            ->where('name', $paymentOption)
            ->update(['is_active' => 1]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
