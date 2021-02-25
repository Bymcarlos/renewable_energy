<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Timesheet;
use CtoVmm\Timecat;
use CtoVmm\Timesubcat;
use CtoVmm\Timerequest;
use CtoVmm\Timerequestsett;
use CtoVmm\Rating;
use CtoVmm\Ratingbench;
use CtoVmm\Ratingtimerequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TimerequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($timesheet_id,$timecat_id=0,$timesubcat_id=0)
    {
        $timesheet = Timesheet::find($timesheet_id);
        if ($timecat_id==0) {
            $timecat = $timesheet->timecats()->first();
        } else {
            $timecat = Timecat::find($timecat_id);
        }
        if ($timesubcat_id==0) {
            $timesubcat = $timecat->timesubcats()->first();
        } else {
            $timesubcat = Timesubcat::find($timesubcat_id);
        }

        return view('ratingtools.templates.time.sheet')
            ->with('timesheet',$timesheet)
            ->with('timecat',$timecat)
            ->with('timesubcat',$timesubcat);
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
        $timerequest = new Timerequest();
        $timerequest->title = $request->title;
        $timerequest->timesubcat_id = $request->timesubcat_id;
        $total = Timerequest::where('timesubcat_id',$request->timesubcat_id)->count();
        if($request->ordering==null || $request->ordering>$total) {
            $timerequest->ordering = $total+1;
        } else {
            $timerequest->ordering = $request->ordering;
            DB::statement("Update timerequests set ordering=ordering+1 where timesubcat_id=$timerequest->timesubcat_id and ordering>=$timerequest->ordering");
        }
        $timerequest->save();

        //Add this request on ratingtimerequests table for all related ratingbenches
        $ratings = Rating::where('timesheet_id','=',$timerequest->timesubcat()->first()->timecat()->first()->timesheet_id)->get();
        foreach ($ratings as $rating) {
            $ratingbenches = Ratingbench::where('rating_id','=',$rating->id)->get();
            foreach ($ratingbenches as $ratingbench) {
                $ratingtimerequest = new Ratingtimerequest();
                $ratingtimerequest->ratingbench_id = $ratingbench->id;
                $ratingtimerequest->timerequest_id = $timerequest->id;
                $ratingtimerequest->save();
            }
        }

        return redirect()->route('timerequests.index',['timesheet' => $request->timesheet_id,'timecat'=>$request->timecat_id,'timesubcat'=>$request->timesubcat_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Timerequest  $timerequest
     * @return \Illuminate\Http\Response
     */
    public function show(Timerequest $timerequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Timerequest  $timerequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Timerequest $timerequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Timerequest  $timerequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timerequest $timerequest)
    {
        $timerequest->title = $request->title;
        if ($timerequest->ordering != $request->ordering) {
            //Ordering change:
            $total = Timerequest::where('timesubcat_id',$request->timesubcat_id)->count();
            if($request->ordering==null || $request->ordering>$total) {
                $new_ordering = $total;
            } else {
                $new_ordering = $request->ordering;
            }
            if ($new_ordering>$timerequest->ordering)
                DB::statement("Update timerequests set ordering=ordering-1 where timesubcat_id=$timerequest->timesubcat_id and ordering>$timerequest->ordering and ordering<=$new_ordering");
            if ($new_ordering<$timerequest->ordering)
                DB::statement("Update timerequests set ordering=ordering+1 where timesubcat_id=$timerequest->timesubcat_id and ordering>=$new_ordering and ordering<$timerequest->ordering");
            $timerequest->ordering = $new_ordering;
        }
        $timerequest->update();

        return redirect()->route('timerequests.index',['timesheet' => $request->timesheet_id,'timecat'=>$request->timecat_id,'timesubcat'=>$request->timesubcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Timerequest  $timerequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timerequest $timerequest)
    {
        $ordering = $timerequest->ordering;
        $timerequest->delete();
        DB::statement("Update timerequests set ordering=ordering-1 where timesubcat_id=$timerequest->timesubcat_id and ordering>$ordering");
        //Remove from ratingtimerequests:
        DB::statement("Delete from ratingtimerequests where timerequest_id=$timerequest->id");
        return redirect()->route('timerequests.index',['timesheet' => $timerequest->timesubcat()->first()->timecat()->first()->timesheet_id,'timecat'=>$timerequest->timesubcat()->first()->timecat_id,'timesubcat'=>$timerequest->timesubcat_id]);
    }

    public function getTimerequestSetts($timerequest_id) {
        $timerequestsetts = Timerequestsett::where('timerequest_id','=',$timerequest_id)->orderBy('id','Asc')->get();
        return Response::json($timerequestsetts);
    }

    public function setTimerequestSetts(Request $request, $timerequest_id) {
        //dd($request);
        $timerequestsetts = Timerequestsett::where('timerequest_id','=',$timerequest_id)->get()->keyBy('id');
        foreach ($timerequestsetts as $timerequestsett_id => $timerequestsett) {
            $field = "value_$timerequestsett_id";
            $timerequestsett->value = $request->$field;
            $timerequestsett->update();
        }
        //To verify that user has reviewed this settings values:
        $timerequest = Timerequest::find($timerequest_id);
        $timerequest->state=0;
        $timerequest->update();

        return redirect()->route('timerequests.index',['timesheet' => $request->timesheet_id,'timecat'=>$request->timecat_id,'timesubcat'=>$request->timesubcat_id]);
    }
}
