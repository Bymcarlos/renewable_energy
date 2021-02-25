<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Subcat;
use Illuminate\Http\Request;

class SubcatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $subcat = new Subcat();
        $subcat->title=$request->title;
        $subcat->cat_id = $request->cat_id;
        $subcat->save();

        return redirect()->route('features.index', ['id' => $request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$subcat->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Subcat  $subcat
     * @return \Illuminate\Http\Response
     */
    public function show(Subcat $subcat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Subcat  $subcat
     * @return \Illuminate\Http\Response
     */
    public function edit(Subcat $subcat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Subcat  $subcat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subcat $subcat)
    {
        $subcat->title = $request->title;
        $subcat->update();
        return redirect()->route('features.index', ['id' => $subcat->cat()->first()->sheet_id,'cat' =>$subcat->cat_id,'subcat'=>$subcat->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Subcat  $subcat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subcat $subcat)
    {
        //Remove subcats, and features:
        $sheet_id = $subcat->cat()->first()->sheet_id;
        $cat_id = $subcat->cat_id;
        $subcat->delete();
        return redirect()->route('features.index', ['id' => $sheet_id,'cat' =>$cat_id]);
    }
}
