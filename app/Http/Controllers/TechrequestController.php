<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Techsheet;
use CtoVmm\Techcat;
use CtoVmm\Techrequest;
use CtoVmm\Criticality;
use CtoVmm\Criteria;
use CtoVmm\Criteriafunc;
use CtoVmm\Feature;
use CtoVmm\Assessment;
use CtoVmm\Rating;
use CtoVmm\Ratingtechrequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class TechrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($techsheet_id,$techcat_id=0)
    {
        $techsheet = Techsheet::find($techsheet_id);
        if ($techcat_id==0) {
            $techcat = $techsheet->techcats()->first();
        } else {
            $techcat = Techcat::find($techcat_id);
        }
        $assessments = Assessment::all();
        $criticalities = Criticality::all();
        $criterias = Criteria::all();
        $area = $techsheet->area()->first();
        return view('ratingtools.templates.technical.sheet')
            ->with('techsheet',$techsheet)
            ->with('area',$area)
            ->with('techcat',$techcat)
            ->with('assessments',$assessments)
            ->with('criticalities',$criticalities)
            ->with('criterias',$criterias);
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
        $techrequest = new Techrequest();
        $techrequest->title = $request->title;
        $techrequest->help = $request->help;
        $techrequest->techcat_id = $request->techcat_id;
        $techrequest->feature_id = $request->feature_id;
        if ($request->inputrequest_id && $request->inputrequest_id>0) {
            $techrequest->inputrequest_id=$request->inputrequest_id;
        }
        $techrequest->criticality_id = $request->criticality_id;
        $techrequest->criteriafunc_id = $request->criteriafunc_id;
        //Check if factor is a valid number (integer or decimal). By default 1.0
        if (strlen($request->inputfactor)>0) {
            $criteria_factor = str_replace(",", ".", $request->inputfactor);
            if (is_numeric($criteria_factor)) {
                $techrequest->inputfactor = $criteria_factor;
            }
        } 
        //Criteria values and/or range:
        $criteriafunc = Criteriafunc::find($request->criteriafunc_id);
        if ($criteriafunc->askvalue) {
            if (strlen($request->criteria_value)>0) {
                $value = str_replace(",", ".", $request->criteria_value);
                if (is_numeric($value)) {
                    $techrequest->value = $value;
                }
            } 
        }
        if ($criteriafunc->askrange) {
            if (strlen($request->criteria_range_x)>0 && strlen($request->criteria_range_y)>0) {
                $range_x = str_replace(",", ".", $request->criteria_range_x);
                $range_y = str_replace(",", ".", $request->criteria_range_y);
                if (is_numeric($range_x) && is_numeric($range_y)) {
                    $techrequest->range_x = $range_x;
                    $techrequest->range_y = $range_y;
                }
            } 
        }
        $total = Techrequest::where('techcat_id',$request->techcat_id)->count();
        if($request->ordering==null || $request->ordering>$total) {
            $techrequest->ordering = $total+1;
        } else {
            $techrequest->ordering = $request->ordering;
            DB::statement("Update techrequests set ordering=ordering+1 where techcat_id=$techrequest->techcat_id and ordering>=$techrequest->ordering");
        }
        $techrequest->save();

        //Create this techrequest in all the ratingtechrequest relationed
        $ratings = Rating::where('techsheet_id','=',$techrequest->techcat()->first()->techsheet_id)->get();
        //Create in each one of this ratings, new ratingtechrequest:
        foreach ($ratings as $rating) {
            $ratingtechrequest = new Ratingtechrequest();
            $ratingtechrequest->rating_id=$rating->id;
            $ratingtechrequest->techrequest_id=$techrequest->id;
            $ratingtechrequest->criticality_id=$techrequest->criticality_id;
            $ratingtechrequest->save();
        }
        

        return redirect()->route('techrequests.index',['techsheet' => $request->techsheet_id,'techcat'=>$techrequest->techcat_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Techrequest  $techrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Techrequest $techrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Techrequest  $techrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Techrequest $techrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Techrequest  $techrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Techrequest $techrequest)
    {
        $techrequest->title = $request->title;
        $techrequest->help = $request->help;
        $techrequest->feature_id = $request->feature_id;
        if ($request->inputrequest_id && $request->inputrequest_id>0) {
            $techrequest->inputrequest_id=$request->inputrequest_id;
        }
        $techrequest->criticality_id = $request->criticality_id;
        $techrequest->criteriafunc_id = $request->criteriafunc_id;
        //Check if factor is a valid number (integer or decimal). By default 1.0
        if (strlen($request->inputfactor)>0) {
            $criteria_factor = str_replace(",", ".", $request->inputfactor);
            if (is_numeric($criteria_factor)) {
                $techrequest->inputfactor = $criteria_factor;
            }
        } 
        //Criteria values and/or range:
        $criteriafunc = Criteriafunc::find($request->criteriafunc_id);
        if ($criteriafunc->askvalue) {
            if (strlen($request->criteria_value)>0) {
                $value = str_replace(",", ".", $request->criteria_value);
                if (is_numeric($value)) {
                    $techrequest->value = $value;
                }
            } 
        }
        if ($criteriafunc->askrange) {
            if (strlen($request->criteria_range_x)>0 && strlen($request->criteria_range_y)>0) {
                $range_x = str_replace(",", ".", $request->criteria_range_x);
                $range_y = str_replace(",", ".", $request->criteria_range_y);
                if (is_numeric($range_x) && is_numeric($range_y)) {
                    $techrequest->range_x = $range_x;
                    $techrequest->range_y = $range_y;
                }
            } 
        }
        if ($techrequest->ordering != $request->ordering) {
            //Ordering change:
            $total = Techrequest::where('techcat_id',$request->techcat_id)->count();
            if($request->ordering==null || $request->ordering>$total) {
                $new_ordering = $total;
            } else {
                $new_ordering = $request->ordering;
            }
            if ($new_ordering>$techrequest->ordering)
                DB::statement("Update techrequests set ordering=ordering-1 where techcat_id=$techrequest->techcat_id and ordering>$techrequest->ordering and ordering<=$new_ordering");
            if ($new_ordering<$techrequest->ordering)
                DB::statement("Update techrequests set ordering=ordering+1 where techcat_id=$techrequest->techcat_id and ordering>=$new_ordering and ordering<$techrequest->ordering");
            $techrequest->ordering = $new_ordering;
        }
        
        $techrequest->update();

        //Update criticality in all the ratingtechrequest relationed
        $ratingtechrequests = Ratingtechrequest::where('techrequest_id','=',$techrequest->id)->get();
        //Update criticality ratingtechrequest:
        foreach ($ratingtechrequests as $ratingtechrequest) {
            $ratingtechrequest->criticality_id=$techrequest->criticality_id;
            $ratingtechrequest->update();
        }
        
        return redirect()->route('techrequests.index',['techsheet' => $request->techsheet_id,'techcat'=>$techrequest->techcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Techrequest  $techrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Techrequest $techrequest)
    {
        $ordering = $techrequest->ordering;
        $techcat = $techrequest->techcat()->first();
        $techrequest->delete();
        DB::statement("Update techrequests set ordering=ordering-1 where techcat_id=$techrequest->techcat_id and ordering>$ordering");
        //Remove from ratingtechrequests:
        DB::statement("Delete from ratingtechrequests where techrequest_id=$techrequest->id");
        return redirect()->route('techrequests.index',['techsheet' => $techcat->techsheet_id,'techcat'=>$techcat->id]);
    }

    public function changeApplicable(Request $request) {
        $techcat = Techcat::find($request->techcat_id);
        if ($techcat->applicable_id==1)
            $techcat->applicable_id = 3;
        else
            $techcat->applicable_id = 1;
        $techcat->update();

        //Update all the ratingtechcats relationed to this template.
        $ratingtechcats = $techcat->ratingtechcats()->get();
        foreach ($ratingtechcats as $ratingtechcat) {
            $ratingtechcat->applicable_id = $techcat->applicable_id;
            $ratingtechcat->update();
        }

        return redirect()->route('techrequests.index',['techsheet' => $techcat->techsheet_id,'techcat'=>$techcat->id]);
    }

    public function getCriteriaFuncs(Request $request) {
        $feature = Feature::find($request->feature_id);

        $list = array();

        if ($request->criticality_id==1 || $request->criticality_id==4) {
            $criterias = Criteria::where('id','=',1)->get();
        } else {
            $criterias = Criteria::all();
        }

        //Custom case: date responsetype = numeric responsetype (best solution create new criteriafuncs on database related to responsetype 4)
        if ($feature->responsetype_id==4)
            $responsetype_id = 3;
        else
            $responsetype_id = $feature->responsetype_id;
        
        foreach ($criterias as $criteria) {
            $funcs = array();
            foreach ($criteria->criteriafuncs()->get() as $criteriafunc) {
                if ($criteriafunc->responsetype_id == $responsetype_id)
                    $funcs[] = ['id'=>$criteriafunc->id,'title'=>$criteriafunc->title,'responsetype'=>$feature->responsetype_id,'askinput'=>$criteriafunc->askinput,'askvalue'=>$criteriafunc->askvalue,'askrange'=>$criteriafunc->askrange];
            }
            $list[] = ['criteria'=>$criteria->title,'criteriafuncs'=>$funcs];
        }
        return Response::json($list);
    }
}
