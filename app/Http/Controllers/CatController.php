<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Cat;
use CtoVmm\Subcat;

class CatController extends Controller
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
        $cat = new Cat();
        $cat->title = $request->title;
        $cat->sheet_id = $request->sheet_id;
        $cat->save();

        $subcat = new Subcat();
        $subcat->title=$request->subcat_title;
        $subcat->cat_id = $cat->id;
        $subcat->save();

        return redirect()->route('features.index', ['id' => $request->sheet_id,'cat' =>$cat->id,'subcat'=>$subcat->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function show(Cat $cat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function edit(Cat $cat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cat $cat)
    {
        $cat->title = $request->title;
        $cat->update();
        return redirect()->route('features.index', ['id' => $request->sheet_id,'cat' =>$cat->id,'subcat'=>$request->subcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cat $cat)
    {
        //Remove cat, subcats, and features:
        $sheet_id = $cat->sheet_id;
        $cat->delete();
        return redirect()->route('features.index', ['id' => $sheet_id]);
    }
}
