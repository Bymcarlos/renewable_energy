<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Inputsheet;
use CtoVmm\Inputcat;
use CtoVmm\Inputrequest;
use CtoVmm\Area;
use Illuminate\Http\Request;

class InputsheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($area_id)
    {
        $inputsheets = Inputsheet::where('area_id','=',$area_id)->get();
        $area = Area::find($area_id);
        return view('ratingtools.templates.inputdata.list')
            ->with('inputsheets',$inputsheets)
            ->with('area',$area);
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
        $inputsheet = new Inputsheet();
        $inputsheet->title = $request->title;
        $inputsheet->area_id = $request->area_id;
        $inputsheet->description = $request->description;        
        $inputsheet->save();
        //Check if caming inputsheet_id (New empty template, or create from other)
        if (isset($request->inputsheet_id) && $request->inputsheet_id>0) {
            //New from other
            $inputsheet_from = Inputsheet::find($request->inputsheet_id);
            //Create same categories with same requests:
            foreach ($inputsheet_from->inputcats()->get() as $inputcat_from) {
                $inputcat = new Inputcat();
                $inputcat->title = $inputcat_from->title;
                $inputcat->inputsheet_id = $inputsheet->id;
                $inputcat->save();
                //Now al requests:
                foreach ($inputcat_from->inputrequests()->get() as $inputrequest_from) {
                    $inputrequest = new Inputrequest();
                    $inputrequest->title = $inputrequest_from->title;
                    $inputrequest->help = $inputrequest_from->help;
                    $inputrequest->order = $inputrequest_from->order;
                    $inputrequest->inputcat_id = $inputcat->id;
                    $inputrequest->save();
                }
            }
        } else {
            //New empty template: Create a default input category called 'Test Specimen':
            $inputcat = new Inputcat();
            $inputcat->title = "General test";
            $inputcat->inputsheet_id =$inputsheet->id;
            $inputcat->save();
        }
        return redirect()->route('inputsheets.index',['area_id'=>$inputsheet->area_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Inputsheet  $inputsheet
     * @return \Illuminate\Http\Response
     */
    public function show(Inputsheet $inputsheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Inputsheet  $inputsheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Inputsheet $inputsheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Inputsheet  $inputsheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inputsheet $inputsheet)
    {
        $inputsheet->title = $request->title;
        $inputsheet->description = $request->description;
        $inputsheet->update();
        return redirect()->route('inputsheets.index',['area_id'=>$inputsheet->area_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Inputsheet  $inputsheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inputsheet $inputsheet)
    {
        $inputsheet->delete();
        return redirect()->route('inputsheets.index',['area_id'=>$inputsheet->area_id]);
    }

    public function areas() {
        $areas = Area::all();
        return view('ratingtools.templates.inputdata.areas')
            ->with('areas',$areas);
    }
}
