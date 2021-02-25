<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Rating;
use CtoVmm\Techcat;
use CtoVmm\Criticality;
use CtoVmm\Applicable;
use CtoVmm\Ratingtechrequest;
use CtoVmm\Ratingtechcat;
use Illuminate\Http\Request;

class RatingtechrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rating_id,$techcat_id=0)
    {
        $rating = Rating::find($rating_id);
        if ($techcat_id==0) {
            $techcat = $rating->techsheet()->first()->techcats()->first();
        } else {
            $techcat = Techcat::find($techcat_id);
        }
        $ratingtechcat = $techcat->ratingtechcats()->where('rating_id','=',$rating->id)->first();
        $applicables = Applicable::where('rating','=','1')->get();
        $criticalities = Criticality::where('type','=','1')->get();
        return view('ratingtools.ratings.technical')
            ->with('rating',$rating)
            ->with('techcat',$techcat)
            ->with('ratingtechcat',$ratingtechcat)
            ->with('applicables',$applicables)
            ->with('criticalities',$criticalities);
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
     * @param  \CtoVmm\Ratingtechrequest  $ratingtechrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Ratingtechrequest $ratingtechrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratingtechrequest  $ratingtechrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratingtechrequest $ratingtechrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratingtechrequest  $ratingtechrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratingtechrequest $ratingtechrequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratingtechrequest  $ratingtechrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratingtechrequest $ratingtechrequest)
    {
        //
    }

    public function changeApplicable(Request $request) {
        $ratingtechcat = Ratingtechcat::find($request->ratingtechcat_id);
        $ratingtechcat->applicable_id = $request->applicable_id;
        $ratingtechcat->update();

        return redirect()->route('ratingtechrequests.index',['rating' => $ratingtechcat->rating_id,'techcat'=>$ratingtechcat->techcat_id]);
    }

    public function changeCriticality(Request $request) {
        $ratingtechrequest = Ratingtechrequest::find($request->ratingtechrequest_id);
        $ratingtechrequest->criticality_id = $request->criticality_id;
        $ratingtechrequest->update();

        return redirect()->route('ratingtechrequests.index',['rating' => $request->rating_id,'techcat'=>$request->techcat_id]);
    }
}
