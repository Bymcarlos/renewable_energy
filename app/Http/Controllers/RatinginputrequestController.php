<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Rating;
use CtoVmm\Inputcat;
use CtoVmm\Ratinginputrequest;
use Illuminate\Http\Request;

class RatinginputrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rating_id,$inputcat_id=null)
    {
        $rating = Rating::find($rating_id);
        if ($inputcat_id==0) {
            $inputcat = $rating->techsheet()->first()->inputsheet()->first()->inputcats()->first();
        } else {
            $inputcat = Inputcat::find($inputcat_id);
        }
        $inputrequests = $rating->ratinginputrequests()->get()->keyBy('inputrequest_id');
        return view('ratingtools.ratings.inputdata')
            ->with('rating',$rating)
            ->with('inputcat',$inputcat)
            ->with('inputrequests',$inputrequests);
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Ratinginputrequest  $ratinginputrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Ratinginputrequest $ratinginputrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratinginputrequest  $ratinginputrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratinginputrequest $ratinginputrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratinginputrequest  $ratinginputrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratinginputrequest $ratinginputrequest)
    {
        $ratinginputrequest->value=$request->value;
        $ratinginputrequest->update();

        return redirect()->route('ratinginputrequests.index',['rating' => $ratinginputrequest->rating_id, 'inputcat'=>$request->inputcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratinginputrequest  $ratinginputrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratinginputrequest $ratinginputrequest)
    {
        //
    }
}
