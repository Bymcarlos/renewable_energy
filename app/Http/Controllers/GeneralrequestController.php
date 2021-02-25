<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Generalrequest;
use CtoVmm\Generalsheet;
use CtoVmm\Section;
use CtoVmm\Partner;
use Illuminate\Http\Request;

class GeneralrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($generalsheet_id,$section_id=null)
    {
        $generalsheet = Generalsheet::find($generalsheet_id);
        if (isset($section_id)) 
            $section = Section::find($section_id);
        else
            $section = $generalsheet->sections()->first();
        return view('partners.templates.sheet')
            ->with('generalsheet',$generalsheet)
            ->with('section',$section);
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
        $generalrequest = new Generalrequest();
        $generalrequest->section_id = $request->section_id;
        $generalrequest->title = $request->title;
        if (isset($request->help))
            $generalrequest->help = $request->help;
        $generalrequest->save();
        //Add this new generalrequest to all partners, on generalrequest_partner table
        $partners = Partner::all();
        foreach ($partners as $partner) {
            $partner->generalrequests()->attach($generalrequest);
        }

        return redirect()->route('generalrequests.index', ['generalsheet_id' => $request->generalsheet_id,'section_id' =>$request->section_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Generalrequest  $generalrequest
     * @return \Illuminate\Http\Response
     */
    public function show(Generalrequest $generalrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Generalrequest  $generalrequest
     * @return \Illuminate\Http\Response
     */
    public function edit(Generalrequest $generalrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Generalrequest  $generalrequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Generalrequest $generalrequest)
    {
        $generalrequest->title = $request->title;
        $generalrequest->help = $request->help;
        $generalrequest->update();
        
        return redirect()->route('generalrequests.index', ['generalsheet_id' => $request->generalsheet_id,'section_id' =>$request->section_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Generalrequest  $generalrequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Generalrequest $generalrequest)
    {
        $generalrequest->delete();
        return redirect()->route('generalrequests.index', ['generalsheet_id' => $generalrequest->section()->first()->generalsheet_id,'section_id' =>$generalrequest->section_id]);
    }
}
