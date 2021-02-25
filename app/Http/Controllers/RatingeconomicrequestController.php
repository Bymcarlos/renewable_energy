<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Ratingeconomicrequest;
use CtoVmm\Rating;
use CtoVmm\Ratingbench;
use CtoVmm\Economiccat;
use CtoVmm\Economicsubcat;
use CtoVmm\Ratingrequeststate;
use Illuminate\Http\Request;

class RatingeconomicrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ratingbench,$economiccat_id=0,$economicsubcat_id=0)
    {
        $ratingbench = Ratingbench::find($ratingbench);
        $rating = Rating::find($ratingbench->rating_id);
        if ($economiccat_id==0) {
            $economiccat = $ratingbench->rating()->first()->economicsheet()->first()->economiccats()->first();
        } else {
            $economiccat = Economiccat::find($economiccat_id);
        }
        if ($economicsubcat_id==0) {
            $economicsubcat = $economiccat->economicsubcats()->first();
        } else {
            $economicsubcat = Economicsubcat::find($economicsubcat_id);
        }
        $economicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');

        $ratingrequeststates = Ratingrequeststate::all();
        $statecolors = ['','white','khaki','Palegreen'];
        return view('ratingtools.ratings.economic')
            ->with('ratingbench',$ratingbench)
            ->with('rating',$rating)
            ->with('economiccat',$economiccat)
            ->with('economicsubcat',$economicsubcat)
            ->with('economicrequests',$economicrequests)
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
     * @param  \CtoVmm\Ratingeconomicrequest  $ratingeconomicrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Ratingeconomicrequest $ratingeconomicrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratingeconomicrequest  $ratingeconomicrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratingeconomicrequest $ratingeconomicrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratingeconomicrequest  $ratingeconomicrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratingeconomicrequest $ratingeconomicrequest)
    {
        $ratingeconomicrequest->value = $request->value;
        $ratingeconomicrequest->ratingrequeststate_id = $request->state;
        $ratingeconomicrequest->update();
        return redirect()->route('ratingeconomicrequests.index',['ratingbench' => $ratingeconomicrequest->ratingbench_id,'economiccat'=>$request->economiccat_id,'economicsubcat'=>$request->economicsubcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratingeconomicrequest  $ratingeconomicrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratingeconomicrequest $ratingeconomicrequest)
    {
        //
    }
}
