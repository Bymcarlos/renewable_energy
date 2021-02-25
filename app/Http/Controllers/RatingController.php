<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Rating;
use CtoVmm\Area;
use CtoVmm\Techsheet;
use CtoVmm\Inputsheet;
use CtoVmm\Timesheet;
use CtoVmm\Economicsheet;
use CtoVmm\Ratinginputrequest;
use CtoVmm\Ratingtechcat;
use CtoVmm\Ratingtechrequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($area_id)
    {
        $area = Area::find($area_id);
        $ratings = Rating::where('area_id','=',$area_id)->get();
        //NECESSARY VALIDATIONS TO CHECK IF WE CAN CALCULATE (AND SHOW) THE SCORE ASSOCIATED TO EACH RATING:

        //Check if the rating has assigned benches:
        $rating_benches_count = DB::table('ratingbenches')
            ->select('rating_id', DB::raw('count(bench_id) as benches'))
            ->groupBy('rating_id')
            ->get()
            ->keyBy('rating_id');
        
        //For create new ratings:
        $areas = Area::all();
        $techsheets = Techsheet::where('area_id','=',$area->id)->get();
        $timesheets = Timesheet::all();
        $economicsheets = Economicsheet::all();

        return view('ratingtools.ratings.list')
            ->with('areas',$areas)
            ->with('area',$area)
            ->with('techsheets',$techsheets)
            ->with('timesheets',$timesheets)
            ->with('economicsheets',$economicsheets)
            ->with('rating_benches_count',$rating_benches_count)
            ->with('ratings',$ratings);
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
        //Create new Rating:
        $techsheet = Techsheet::find($request->techsheet_id);
        $inputsheet = Inputsheet::find($techsheet->inputsheet_id);
        $timesheet = Timesheet::find($request->timesheet_id);
        $economicsheet = Economicsheet::find($request->economicsheet_id);

        $rating = new Rating();
        $rating->title = $request->title;
        $rating->description = $request->description;
        $rating->area_id = $request->area_id;
        $rating->techsheet_id = $techsheet->id;
        $rating->timesheet_id = $timesheet->id;
        $rating->economicsheet_id = $economicsheet->id;
        $rating->save();
        //Add inputrequests to ratinginputrequests:
        foreach ($inputsheet->inputcats()->get() as $inputcat) {
            foreach ($inputcat->inputrequests()->get() as $inputrequest) {
                Ratinginputrequest::create(['rating_id'=>$rating->id,'inputrequest_id'=>$inputrequest->id]);
            }
        }
        //Add Techcat and Techrequests to ratingtechcats and ratingtechrequests:
        foreach ($techsheet->techcats()->get() as $techcat) {
            Ratingtechcat::create(['rating_id'=>$rating->id,'techcat_id'=>$techcat->id,'applicable_id'=>$techcat->applicable_id]);
            foreach ($techcat->techrequests()->get() as $techrequest) {
                Ratingtechrequest::create(['rating_id'=>$rating->id,'techrequest_id'=>$techrequest->id,'criticality_id'=>$techrequest->criticality_id]);
            }
        }
        return redirect()->route('ratings.index',['area_id'=>$rating->area_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function show(Rating $rating)
    {
        //NECESSARY VALIDATIONS TO CHECK IF WE CAN CALCULATE (AND SHOW) THE SCORE ASSOCIATED TO RATING:

        //Check if the rating has assigned benches:
        $rating_benches_count = DB::table('ratingbenches')
            ->select('rating_id', DB::raw('count(bench_id) as benches'))
            ->where('rating_id','=',$rating->id)
            ->groupBy('rating_id')
            ->get()
            ->keyBy('rating_id');
        //Check if the rating has techcats pending define applicability (applicable_id=3)
        $ratingtechcats_applicable_pending = array();
        //Count inputrequest pending values for each rating:
        $rating_inputrequests_pending = array();
        //Count number of requirements of each criticality (To check at least, we have 1 requirement of each criticality)
        $rating_criticalities = array();
        
        //Initialize techcats applicables list of the rating:
        $techcats = array();
        //Initialize count of techcats pending define applicable for this rating:
        $ratingtechcats_applicable_pending[$rating->id]=0;
        //Initialize inputrequest pending values for this rating:
        $rating_inputrequests_pending[$rating->id]=0;
        //For each techcat of the rating:
        foreach ($rating->ratingtechcats()->get() as $ratingtechcat) {
            //Check if is applicable, not or undefined:
            switch($ratingtechcat->applicable_id) {
                case 1: //Applicable YES: 
                    //Add to the rating techcats applicables list:
                    $techcats[] = $ratingtechcat->techcat_id;
                    //Check if has some requirement associate with an inputrequest with pending value (null):
                    $pending_inputs = DB::table('techrequests')
                        ->select(DB::raw('count(inputrequests.id) as total'))
                        ->join('inputrequests', 'techrequests.inputrequest_id', '=', 'inputrequests.id')
                        ->join('ratinginputrequests', 'inputrequests.id', '=', 'ratinginputrequests.inputrequest_id')
                        ->where('techrequests.techcat_id','=',$ratingtechcat->techcat_id)
                        ->where('ratinginputrequests.rating_id','=',$rating->id)
                        ->where('ratinginputrequests.value','=',null)
                        ->groupBy('inputrequests.id','ratinginputrequests.value')
                        ->first();
                    if (isset($pending_inputs))
                        $rating_inputrequests_pending[$rating->id] += $pending_inputs->total;
                    break;
                case 2: //Applicable NO
                    break;
                case 3: //Applicable undefined
                    $ratingtechcats_applicable_pending[$rating->id]++;
                    break;
            }
        }
        //Count number of requirements of each criticality in this rating (only in techcats applicables):
        $rating_criticalities[$rating->id] = DB::table('ratingtechrequests')
            ->select('ratingtechrequests.criticality_id', DB::raw('count(*) as total'))
            ->join('techrequests', 'ratingtechrequests.techrequest_id', '=', 'techrequests.id')
            ->where('rating_id','=',$rating->id)
            ->whereIn('techrequests.techcat_id',$techcats)
            ->groupBy('ratingtechrequests.criticality_id')
            ->orderby('criticality_id','asc')
            ->get()
            ->keyBy('criticality_id');

        //V2: Deprecated:
        //$benches_timing_weeks = array();    //Sum of test execution times category for each bench of the rating
        //$benches_economic_realcost = array(); //Real cost value for each bench of the rating

        //V3: Added new file "ratingrequeststate" to check if each value in Time and Economic ratingrequests have a reviewed value:
            //1->Undefined (Can not show the score)
            //2,3 (estimated, confirmed) Can show the score
        $benches_time_request_undefined = array(); //Number of rating time request in undefined state
        $benches_economic_request_undefined = array(); //Number of rating economic request in undefined state

        foreach($rating->ratingbenches()->get() as $ratingbench) {
            $bench = $ratingbench->bench()->first();
            /*
            //V2 - Deprecated:
            //Sum of weeks must be >0
            $benches_timing_weeks[$bench->id] = DB::table('ratingtimerequests')
                ->select(DB::raw('sum(value) as total'))
                ->join('timerequests', 'ratingtimerequests.timerequest_id', '=', 'timerequests.id')
                ->where('ratingtimerequests.ratingbench_id','=',$ratingbench->id)
                ->where('timerequests.settable','=',0)
                ->first();
            
            //Sum of costs must be >0
            $benches_economic_realcost[$bench->id] = DB::table('ratingeconomicrequests')
                ->select(DB::raw('sum(value) as total'))
                ->join('economicrequests', 'ratingeconomicrequests.economicrequest_id', '=', 'economicrequests.id')
                ->where('ratingeconomicrequests.ratingbench_id','=',$ratingbench->id)
                ->where('economicrequests.weight','=',0)
                ->first();
            */
            //V3: 
            $benches_economic_request_undefined[$bench->id] = DB::table('ratingeconomicrequests')
                ->select(DB::raw('count(*) as total'))
                ->where('ratingeconomicrequests.ratingbench_id','=',$ratingbench->id)
                ->where('ratingrequeststate_id','=',1)
                ->first();
            $benches_time_request_undefined[$bench->id] = DB::table('ratingtimerequests')
                ->select(DB::raw('count(*) as total'))
                ->where('ratingtimerequests.ratingbench_id','=',$ratingbench->id)
                ->where('ratingrequeststate_id','=',1)
                ->first();
        }

        return view('ratingtools.ratings.rating')
            ->with('rating',$rating)
            ->with('rating_criticalities',$rating_criticalities)
            ->with('ratingtechcats_applicable_pending',$ratingtechcats_applicable_pending)
            ->with('rating_inputrequests_pending',$rating_inputrequests_pending)
            //->with('benches_timing_weeks',$benches_timing_weeks)
            //->with('benches_economic_realcost',$benches_economic_realcost)
            ->with('benches_economic_request_undefined',$benches_economic_request_undefined)
            ->with('benches_time_request_undefined',$benches_time_request_undefined)
            ->with('rating_benches_count',$rating_benches_count);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function edit(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rating $rating)
    {
        $rating->title = $request->title;
        $rating->update();
        return redirect()->route('ratings.index',['area_id'=>$rating->area_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();
        return redirect()->route('ratings.index',['area_id'=>$rating->area_id]);
    }

    public function areas() {
        $areas = Area::all();
        return view('ratingtools.ratings.areas')
            ->with('areas',$areas);
    }
}
