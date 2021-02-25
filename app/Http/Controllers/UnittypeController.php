<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Unittype;
use Illuminate\Http\Request;

class UnittypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unittypes = Unittype::all();
        return view('intranet.unittypes')
            ->with('unittypes',$unittypes);
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
        $unittype = new Unittype();
        $unittype->title = $request->title;
        $unittype->save();
        return redirect()->route('unittypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Unittype  $unittype
     * @return \Illuminate\Http\Response
     */
    public function show(Unittype $unittype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Unittype  $unittype
     * @return \Illuminate\Http\Response
     */
    public function edit(Unittype $unittype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Unittype  $unittype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unittype $unittype)
    {
        $unittype->title = $request->title;
        $unittype->update();
        return redirect()->route('unittypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Unittype  $unittype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unittype $unittype)
    {
        //TODO: Check if exist benches?
        $unittype->delete();
        return redirect()->route('unittypes.index');
    }
}
