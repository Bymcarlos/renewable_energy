<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Platform;
use CtoVmm\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $platform = Platform::find($id);
        return view('intranet.products')
            ->with('platform',$platform);
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
        $product = new Product();
        $product->title = $request->title;
        $product->platform_id = $request->platform_id;
        $product->save();
        return redirect()->route('products.index',['id'=>$request->platform_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Product  $product
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
     * @param  \CtoVmm\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->title = $request->title;
        $product->update();
        return redirect()->route('products.index',['id'=>$product->platform_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $platform_id = $product->platform_id;
        $product->delete();
        return redirect()->route('products.index',['id'=>$platform_id]);
    }
}
