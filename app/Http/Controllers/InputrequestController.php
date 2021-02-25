<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Techsheet;
use CtoVmm\Inputsheet;
use CtoVmm\Inputcat;
use CtoVmm\Inputrequest;
use CtoVmm\Rating;
use CtoVmm\Ratinginputrequest;
use Illuminate\Http\Request;

class InputrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($inputsheet_id,$inputcat_id=0)
    {
        $inputsheet = Inputsheet::find($inputsheet_id);
        if ($inputcat_id==0) {
            $inputcat = $inputsheet->inputcats()->first();
        } else {
            $inputcat = Inputcat::find($inputcat_id);
        }
        return view('ratingtools.templates.inputdata.sheet')
            ->with('inputsheet',$inputsheet)
            ->with('inputcat',$inputcat);
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
        $inputrequest = new Inputrequest();
        $inputrequest->title = $request->title;
        $inputrequest->help = $request->help;
        $inputrequest->inputcat_id = $request->inputcat_id;
        $inputrequest->save();

        //TODO: Insert this new item data in all the ratinginputrequest relationed:
        $techsheet_ids = array();
        //List of techsheets which are using the inputsheet:
        $techsheets = Techsheet::where('inputsheet_id','=',$inputrequest->inputcat()->first()->inputsheet_id)->get();
        foreach($techsheets as $techsheet) {
            $techsheet_ids[] = $techsheet->id;
        }
        $ratings = Rating::whereIn('techsheet_id', $techsheet_ids)->get();
        //Create in each one of this ratings, new ratinginputrequest:
        foreach ($ratings as $rating) {
            $ratinginputrequest = new Ratinginputrequest();
            $ratinginputrequest->rating_id=$rating->id;
            $ratinginputrequest->inputrequest_id=$inputrequest->id;
            $ratinginputrequest->save();
        }

        return redirect()->route('inputrequests.index', ['inputsheet' => $request->inputsheet_id,'inputcat' => $request->inputcat_id]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Inputrequest  $inputrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Inputrequest $inputrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Inputrequest  $inputrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Inputrequest $inputrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Inputrequest  $inputrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inputrequest $inputrequest)
    {
        $inputrequest->title = $request->title;
        $inputrequest->help = $request->help;
        $inputrequest->update();
        return redirect()->route('inputrequests.index', ['inputsheet' => $request->inputsheet_id,'inputcat' => $request->inputcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Inputrequest  $inputrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inputrequest $inputrequest)
    {
        $inputrequest->delete();
        $inputcat = $inputrequest->inputcat()->first();
        return redirect()->route('inputrequests.index', ['inputsheet' => $inputcat->inputsheet_id,'inputcat' => $inputcat->id]);
    }
}
