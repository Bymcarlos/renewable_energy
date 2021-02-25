<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Techsheet;
use CtoVmm\Techcat;
use CtoVmm\Inputsheet;
use CtoVmm\Area;
use CtoVmm\Criticality;
use CtoVmm\Techrequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class TechsheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($area_id)
    {
        $techsheets = Techsheet::where('area_id','=',$area_id)->get();
        /*
        //Count for every techsheet, number of requirements of each criticality
        $techsheets_criticalities = array();
        foreach ($techsheets as $techsheet) {
            $techcats = array();
            foreach ($techsheet->techcats()->get() as $techcat) {
                $techcats[] = $techcat->id;
            }
            $count = DB::table('criticalities')
                ->select('criticalities.id',DB::raw('count(*) as total'))
                ->join('techrequests', 'criticalities.id', '=', 'techrequests.criticality_id')
                ->whereIn('techrequests.techcat_id',$techcats)
                ->groupBy('criticalities.id')
                ->get()
                ->keyBy('id');
            $techsheets_criticalities[$techsheet->id] = $count;
        }
        dd($techsheets_criticalities);
        */
        
        $area = Area::find($area_id);
        $inputsheets = Inputsheet::where('area_id','=',$area_id)->get();
        return view('ratingtools.templates.technical.list')
            ->with('techsheets',$techsheets)
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
        $inputsheet = Inputsheet::find($request->inputsheet_id);
        $techsheet = new Techsheet();
        $techsheet->title = $request->title;
        $techsheet->inputsheet_id = $inputsheet->id;
        $techsheet->area_id = $inputsheet->area_id;
        $techsheet->description = $request->description;
        $techsheet->save();
        
        //Check if new empty template, or create from other:
        if (isset($request->techsheet_id) && $request->techsheet_id>0) {
            //Create from other:
            $techsheet_from = Techsheet::find($request->techsheet_id);
            foreach ($techsheet_from->criticalities()->get() as $criticality) {
                $techsheet->criticalities()->attach($criticality,['score_weight'=>$criticality->pivot->score_weight]);
            }
            //Create techcats:
            foreach ($techsheet_from->techcats()->get() as $techcat_from) {
                $techcat = new Techcat();
                $techcat->title = $techcat_from->title;
                $techcat->applicable_id = $techcat_from->applicable_id;
                $techcat->techsheet_id = $techsheet->id;
                $techcat->save();
                //Now al requests:
                foreach ($techcat_from->techrequests()->get() as $techrequest_from) {
                    $techrequest = new Techrequest();
                    $techrequest->title = $techrequest_from->title;
                    $techrequest->help = $techrequest_from->help;
                    $techrequest->ordering = $techrequest_from->ordering;
                    $techrequest->techcat_id = $techcat->id;
                    $techrequest->inputrequest_id = $techrequest_from->inputrequest_id;
                    $techrequest->feature_id = $techrequest_from->feature_id;
                    $techrequest->criticality_id = $techrequest_from->criticality_id;
                    $techrequest->criteriafunc_id = $techrequest_from->criteriafunc_id;
                    $techrequest->inputfactor = $techrequest_from->inputfactor;
                    $techrequest->value = $techrequest_from->value;
                    $techrequest->range_x = $techrequest_from->range_x;
                    $techrequest->range_y = $techrequest_from->range_y;
                    $techrequest->save();
                }
            }
        } else {  //Create new empty template:
            //Criticalities score_weights relating to this techsheet:
            $criticality = Criticality::find(1);//Primary
            $techsheet->criticalities()->attach($criticality,['score_weight'=>\Config::get('constants.criticalities_weights.primary')]);
            $criticality = Criticality::find(2);//Secondary
            $techsheet->criticalities()->attach($criticality,['score_weight'=>\Config::get('constants.criticalities_weights.secondary')]);
            $criticality = Criticality::find(3);//Tertiary
            $techsheet->criticalities()->attach($criticality,['score_weight'=>\Config::get('constants.criticalities_weights.tertiary')]);
            //Create default category:
            $techcat = new Techcat();
            $techcat->title = "General test";
            $techcat->techsheet_id =$techsheet->id;
            $techcat->applicable_id = 1;
            $techcat->save();
        }
        
        return redirect()->route('techsheets.index',['area_id'=>$techsheet->area_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Techsheet  $techsheet
     * @return \Illuminate\Http\Response
     */
    public function show(Techsheet $techsheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Techsheet  $techsheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Techsheet $techsheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Techsheet  $techsheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Techsheet $techsheet)
    {
        $techsheet->title = $request->title;
        $techsheet->description = $request->description;
        $techsheet->update();
        return redirect()->route('techsheets.index',['area_id'=>$techsheet->area_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Techsheet  $techsheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Techsheet $techsheet)
    {
        $techsheet->delete();
        return redirect()->route('techsheets.index',['area_id'=>$techsheet->area_id]);
    }

    public function areas() {
        $areas = Area::all();
        return view('ratingtools.templates.technical.areas')
            ->with('areas',$areas);
    }

    public function getCriticalitiesWeights($techsheet_id) {
        $techsheet = Techsheet::find($techsheet_id);
        $weights = array();
        foreach ($techsheet->criticalities()->get() as $key => $item) {
            $weights[$item->pivot->criticality_id]["label"] = $item->title;
            $weights[$item->pivot->criticality_id]["weight"] = $item->pivot->score_weight;
        }
        return Response::json($weights);
    }

    public function setCriticalitiesWeights(Request $request, $techsheet_id) {
        $techsheet = Techsheet::find($techsheet_id);
        $criticalities = Criticality::all();
        foreach ($criticalities as $criticality) {
            $field = "weight_".$criticality->id;
            $techsheet->criticalities()->updateExistingPivot($criticality->id, ['score_weight' => $request->$field]);
        }
        return redirect()->route('techsheets.index',['area_id'=>$techsheet->area_id]);
    }
}
