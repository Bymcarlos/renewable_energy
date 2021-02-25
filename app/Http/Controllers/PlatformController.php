<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Customer;
use CtoVmm\Platform;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Only SGRE Platforms:
        $cust_sgre = Customer::where('title','SGRE')->first();
        $platforms = Platform::where('customer_id',$cust_sgre->id)->get();
        return view('intranet.platforms')
            ->with('platforms',$platforms);
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
        $cust_sgre = Customer::where('title','SGRE')->first();
        $platform = new Platform();
        $platform->title = $request->title;
        $platform->customer_id = $cust_sgre->id;
        $platform->save();
        return redirect()->route('platforms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function show(Platform $platform)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function edit(Platform $platform)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Platform $platform)
    {
        $platform->title = $request->title;
        $platform->update();
        return redirect()->route('platforms.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function destroy(Platform $platform)
    {
        $platform->delete();
        return redirect()->route('platforms.index');
    }
}
