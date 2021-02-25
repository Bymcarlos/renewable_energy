<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Economicsheet;
use CtoVmm\Economiccat;
use CtoVmm\Economicrequest;
use CtoVmm\Economicsubcat;
use CtoVmm\Ratingeconomicrequest;
use CtoVmm\Rating;
use CtoVmm\Ratingbench;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EconomicrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($economicsheet_id,$economiccat_id=0,$economicsubcat_id=0)
    {
        $economicsheet = Economicsheet::find($economicsheet_id);
        if ($economiccat_id==0) {
            $economiccat = $economicsheet->economiccats()->first();
        } else {
            $economiccat = Economiccat::find($economiccat_id);
        }
        if ($economicsubcat_id==0) {
            $economicsubcat = $economiccat->economicsubcats()->first();
        } else {
            $economicsubcat = Economicsubcat::find($economicsubcat_id);
        }
        //If this category is weighted, calculate the sum (must be 1)
        if ($economicsubcat->weighted) {
            $weight_sum = $economicsubcat->economicrequests()->get()->sum("weight");
        } else
            $weight_sum = null;


        return view('ratingtools.templates.economic.sheet')
            ->with('economicsheet',$economicsheet)
            ->with('economiccat',$economiccat)
            ->with('economicsubcat',$economicsubcat)
            ->with('weight_sum',$weight_sum);
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
        $economicrequest = new Economicrequest();
        $economicrequest->title = $request->title;
        $economicrequest->economicsubcat_id = $request->economicsubcat_id;
        if (isset($request->weight)) {
            $economicrequest->weight = $request->weight;
        }
        $total = Economicrequest::where('economicsubcat_id',$request->economicsubcat_id)->count();
        if($request->ordering==null || $request->ordering>$total) {
            $economicrequest->ordering = $total+1;
        } else {
            $economicrequest->ordering = $request->ordering;
            DB::statement("Update economicrequests set ordering=ordering+1 where economicsubcat_id=$economicrequest->economicsubcat_id and ordering>=$economicrequest->ordering");
        }
        $economicrequest->save();

        //Add this request on ratingeconomicrequests table for all related ratingbenches:
        $ratings = Rating::where('economicsheet_id','=',$economicrequest->economicsubcat()->first()->economiccat()->first()->economicsheet_id)->get();
        foreach ($ratings as $rating) {
            $ratingbenches = Ratingbench::where('rating_id','=',$rating->id)->get();
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequest = new Ratingeconomicrequest();
                $ratingeconomicrequest->ratingbench_id = $ratingbench->id;
                $ratingeconomicrequest->economicrequest_id = $economicrequest->id;
                $ratingeconomicrequest->save();
            }
        }

        return redirect()->route('economicrequests.index',['economicsheet' => $request->economicsheet_id,'economiccat'=>$request->economiccat_id,'economicsubcat'=>$request->economicsubcat_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Economicrequest  $economicrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Economicrequest $economicrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Economicrequest  $economicrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Economicrequest $economicrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Economicrequest  $economicrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Economicrequest $economicrequest)
    {
        $economicrequest->title = $request->title;
        $economicrequest->help = $request->help;
        if ($economicrequest->ordering != $request->ordering) {
            //Ordering change:
            $total = Economicrequest::where('economicsubcat_id',$request->economicsubcat_id)->count();
            if($request->ordering==null || $request->ordering>$total) {
                $new_ordering = $total;
            } else {
                $new_ordering = $request->ordering;
            }
            if ($new_ordering>$economicrequest->ordering)
                DB::statement("Update economicrequests set ordering=ordering-1 where economicsubcat_id=$economicrequest->economicsubcat_id and ordering>$economicrequest->ordering and ordering<=$new_ordering");
            if ($new_ordering<$economicrequest->ordering)
                DB::statement("Update economicrequests set ordering=ordering+1 where economicsubcat_id=$economicrequest->economicsubcat_id and ordering>=$new_ordering and ordering<$economicrequest->ordering");
            $economicrequest->ordering = $new_ordering;
        }
        $economicrequest->update();

        return redirect()->route('economicrequests.index',['economicsheet' => $request->economicsheet_id,'economiccat'=>$request->economiccat_id,'economicsubcat'=>$request->economicsubcat_id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Economicrequest  $economicrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Economicrequest $economicrequest)
    {
        $ordering = $economicrequest->ordering;
        $economicrequest->delete();
        DB::statement("Update economicrequests set ordering=ordering-1 where economicsubcat_id=$economicrequest->economicsubcat_id and ordering>$ordering");
        //Remove from ratingeconomicrequests:
        DB::statement("Delete from ratingeconomicrequests where economicrequest_id=$economicrequest->id");
        return redirect()->route('economicrequests.index',['economicsheet' => $economicrequest->economicsubcat()->first()->economiccat()->first()->economicsheet_id,'economiccat'=>$economicrequest->economicsubcat()->first()->economiccat_id,'economicsubcat'=>$economicrequest->economicsubcat_id]);
    }
    
    public function setWeight(Request $request, Economicrequest $economicrequest)
    {
        $economicrequest->weight = $request->weight;
        $economicrequest->update();
        return redirect()->route('economicrequests.index',['economicsheet' => $request->economicsheet_id,'economiccat'=>$request->economiccat_id,'economicsubcat'=>$request->economicsubcat_id]);
    }

}
