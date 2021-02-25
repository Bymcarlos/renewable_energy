<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Inputcat;
use Illuminate\Http\Request;

class InputcatController extends Controller
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
        $inputcat = new Inputcat();
        $inputcat->title = $request->title;
        $inputcat->inputsheet_id = $request->inputsheet_id;
        $inputcat->save();
        return redirect()->route('inputrequests.index', ['inputsheet' => $inputcat->inputsheet_id,'inputcat' =>$inputcat->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Inputcat  $inputcat
     * @return \Illuminate\Http\Response
     */
    public function show(Inputcat $inputcat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Inputcat  $inputcat
     * @return \Illuminate\Http\Response
     */
    public function edit(Inputcat $inputcat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Inputcat  $inputcat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inputcat $inputcat)
    {
        $inputcat->title = $request->title;
        $inputcat->update();
        return redirect()->route('inputrequests.index', ['inputsheet' => $inputcat->inputsheet_id,'inputcat' =>$inputcat->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Inputcat  $inputcat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inputcat $inputcat)
    {
        //$input_id = $inputcat->input_id;
        $inputcat->delete();
        return redirect()->route('inputrequests.index', ['inputsheet' => $inputcat->inputsheet_id]);
    }
}
