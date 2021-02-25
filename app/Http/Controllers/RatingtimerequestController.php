<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Ratingtimerequest;
use CtoVmm\Rating;
use CtoVmm\Ratingbench;
use CtoVmm\Timecat;
use CtoVmm\Timesubcat;
use CtoVmm\Ratingrequeststate;
use Illuminate\Http\Request;

class RatingtimerequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ratingbench,$timecat_id=0,$timesubcat_id=0)
    {
        $ratingbench = Ratingbench::find($ratingbench);
        $rating = Rating::find($ratingbench->rating_id);
        if ($timecat_id==0) {
            $timecat = $ratingbench->rating()->first()->timesheet()->first()->timecats()->first();
        } else {
            $timecat = Timecat::find($timecat_id);
        }
        if ($timesubcat_id==0) {
            $timesubcat = $timecat->timesubcats()->first();
        } else {
            $timesubcat = Timesubcat::find($timesubcat_id);
        }
        $timerequests = $ratingbench->ratingtimerequests()->get()->keyBy('timerequest_id');

        $ratingrequeststates = Ratingrequeststate::all();
        $statecolors = ['','white','khaki','Palegreen'];
        return view('ratingtools.ratings.time')
            ->with('ratingbench',$ratingbench)
            ->with('rating',$rating)
            ->with('timecat',$timecat)
            ->with('timesubcat',$timesubcat)
            ->with('timerequests',$timerequests)
            ->with('ratingrequeststates',$ratingrequeststates)
            ->with('statecolors',$statecolors);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Ratingtimerequest  $ratingtimerequest
     * @return \Illuminate\Http\Response
     */
    public function show(Ratingtimerequest $ratingtimerequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratingtimerequest  $ratingtimerequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratingtimerequest $ratingtimerequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratingtimerequest  $ratingtimerequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratingtimerequest $ratingtimerequest)
    {
        $ratingtimerequest->value = $request->value;
        $ratingtimerequest->ratingrequeststate_id = $request->state;
        $ratingtimerequest->update();
        return redirect()->route('ratingtimerequests.index',['ratingbench' => $ratingtimerequest->ratingbench_id,'timecat'=>$request->timecat_id,'timesubcat'=>$request->timesubcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratingtimerequest  $ratingtimerequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratingtimerequest $ratingtimerequest)
    {
        //
    }
}
