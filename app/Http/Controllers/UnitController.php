<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Unittype;
use CtoVmm\Unit;
use Illuminate\Http\Request;
use Log;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $unittype = Unittype::find($id);
        return view('intranet.units')
            ->with('unittype',$unittype);
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
        $unit = new Unit();
        $unit->unittype_id = $request->unittype_id;
        $unit->title = $request->title;
        $unit->save();
        return redirect()->route('units.index', ['id' => $request->unittype_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $unit->title = $request->title;
        $unit->update();
        return redirect()->route('units.index', ['id' => $unit->unittype_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        //TODO: Check if exist benches?
        if ($unit->features()->count()>0 || $unit->featurebrandvalues()->count()>0 || $unit->economicrequests()->count()>0) {
            //Can not to be removed, there are features, item/value fields, or rating tools requirements relating to this unit
            Log::channel('custom')->info("UnitController: Can not remove Unit:$unit->id (there are features, item/value fields, or rating tools requirements relating to this unit)");
        } else {
            $unit->delete();
        }
        return redirect()->route('units.index', ['id' => $unit->unittype_id]);
    }
}
