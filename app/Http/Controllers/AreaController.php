<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Area;

use Illuminate\Support\Facades\Response;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = Area::all();
        return view('intranet.areas')->with('areas',$areas);
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
        $area = new Area();
        $area->title = strtoupper($request->title);
        $area->save();
        return redirect()->route('areas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        $area->title = strtoupper($request->title);
        $area->update();
        return redirect()->route('areas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        //TODO: Check if exist benches?
        $area->delete();
        return redirect()->route('areas.index');
    }

    public function list(Request $request) {
        $area = Area::find($request->area_id);
        $components = $area->components()->orderBy('title')->get();
        return Response::json($components);
    }

    /*
    public function listTechsheets(Request $request) {
        $area = Area::find($request->area_id);
        $techsheets = $area->techsheets()->orderBy('title')->get();
        return Response::json($techsheets);
    }
    */
}
