<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Techcat;
use CtoVmm\Rating;
use CtoVmm\Ratingtechcat;
use Illuminate\Http\Request;

class TechcatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $techcat = new Techcat();
        $techcat->title = $request->title;
        $techcat->techsheet_id = $request->techsheet_id;
        $techcat->applicable_id = 3;
        $techcat->save();

        //TODO: Create ratingtechcat with the applicable field for each Rating which use this techsheet:
        $ratings = Rating::where('techsheet_id','=',$techcat->techsheet_id)->get();
        //Create in each one of this ratings, new ratingtechcat:
        foreach ($ratings as $rating) {
            $ratingtechcat = new Ratingtechcat();
            $ratingtechcat->rating_id=$rating->id;
            $ratingtechcat->techcat_id=$techcat->id;
            $ratingtechcat->applicable_id=$techcat->applicable_id;
            $ratingtechcat->save();
        }

        return redirect()->route('techrequests.index',['techsheet' => $techcat->techsheet_id,'techcat' => $techcat->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Techcat  $techcat
     * @return \Illuminate\Http\Response
     */
    public function show(Techcat $techcat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Techcat  $techcat
     * @return \Illuminate\Http\Response
     */
    public function edit(Techcat $techcat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Techcat  $techcat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Techcat $techcat)
    {
        $techcat->title = $request->title;
        $techcat->update();
        return redirect()->route('techrequests.index',['techsheet' => $techcat->techsheet_id,'techcat' => $techcat->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Techcat  $techcat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Techcat $techcat)
    {
        $techcat->delete();
        return redirect()->route('techrequests.index',['techsheet' => $techcat->techsheet_id]);

    }
}
