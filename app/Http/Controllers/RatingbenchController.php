<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Rating;
use CtoVmm\Ratingbench;
use CtoVmm\Bench;
use CtoVmm\Ratingtimerequest;
use CtoVmm\Ratingeconomicrequest;
use CtoVmm\AreaComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingbenchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rating_id)
    {
        $rating = Rating::find($rating_id);
        return view('ratingtools.ratings.benches')
            ->with('rating',$rating);
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
     * @param  \CtoVmm\Ratingbench  $ratingbench
     * @return \Illuminate\Http\Response
     */
    public function show(Ratingbench $ratingbench)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratingbench  $ratingbench
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratingbench $ratingbench)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratingbench  $ratingbench
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratingbench $ratingbench)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratingbench  $ratingbench
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratingbench $ratingbench)
    {
        //
    }

    public function ratingBenchesSelection($rating_id,$component_id=0) {
        $rating = Rating::find($rating_id);
        if($component_id==0) {
            $area_component=null;
            //Select * from benches where area_component_id in (Select id from area_component where area_id=1 ORDER BY `area_id` ASC)
            $result = DB::table('benches')
                ->select('benches.id','benches.title')
                ->join('area_component','benches.area_component_id','=','area_component.id')
                ->where('area_component.area_id','=',$rating->area_id)
                ->where('benchtype_id','=',1)   //only real benches, type 2 are templates for "search by parameters" reports
                ->orderby('benches.title','asc')
                ->get();
            
        } else {
            $area_component = AreaComponent::where('area_id','=',$rating->area_id)->where('component_id','=',$component_id)->first();
            $result = Bench::where('area_component_id','=',$area_component->id)->where('benchtype_id','=',1)->orderby('benches.title','asc')->get();
        }
        $benches=array();
        foreach ($result as $item) {
            //Calculate % of null feature values:
            $sql = "Select count(*) as total,sum(case when bench_feature.value is NULL then 0 else 1 end) as with_value from bench_feature inner join (SELECT distinct(techrequests.feature_id) as TECH_FEATURE from techrequests where techcat_id in (select id from techcats where techsheet_id='$rating->techsheet_id')) as TECH_FEATURES where bench_feature.feature_id = TECH_FEATURES.TECH_FEATURE and bench_feature.bench_id = $item->id";
            $values = DB::select($sql);
            $benches[$item->id]["bench"] = Bench::find($item->id);
            $benches[$item->id]["values"] = ($values[0]->with_value/$values[0]->total)*100;
        }
        //Current selected benches key by bench_id:
        $ratingbenches = Ratingbench::where('rating_id','=',$rating->id)->get()->keyBy('bench_id');
        return view('ratingtools.ratings.benches_selection')
            ->with('benches',$benches)
            ->with('component_id',$component_id)
            ->with('ratingbenches',$ratingbenches)
            ->with('rating',$rating);
    }

    public function ratingBenchState($rating_id,$bench_id,$component_id,$ratingbench) {
        if ($ratingbench==0){
            //Add bench to the rating
            $ratingbench = new Ratingbench();
            $ratingbench->rating_id = $rating_id;
            $ratingbench->bench_id = $bench_id;
            $ratingbench->save();
            //Add new timing template requests associated to this ratingbench:
            $rating = Rating::find($rating_id);
            foreach ($rating->timesheet()->first()->timecats()->get() as $timecat) {
                foreach ($timecat->timesubcats()->get() as $timesubcat) {
                    foreach ($timesubcat->timerequests()->get() as $timerequest) {
                        Ratingtimerequest::create(['ratingbench_id'=>$ratingbench->id,'timerequest_id'=>$timerequest->id]);
                    }
                }
            }
            //Add new economic template requests associated to this ratingbench
            foreach ($rating->economicsheet()->first()->economiccats()->get() as $economiccat) {
                foreach ($economiccat->economicsubcats()->get() as $economicsubcat) {
                    foreach ($economicsubcat->economicrequests()->get() as $economicrequest) {
                        Ratingeconomicrequest::create(['ratingbench_id'=>$ratingbench->id,'economicrequest_id'=>$economicrequest->id]);
                    }
                }
            }
        } else {
            //Remove bench from the rating
            $ratingbench = Ratingbench::find($ratingbench);
            $ratingbench->delete();
        }
        //return redirect()->action('RatingbenchController@ratingBenchesSelection',['rating'=>$rating_id,'component'=>$component_id]);
        return $this->ratingBenchesSelection($rating_id,$component_id);
    }
}