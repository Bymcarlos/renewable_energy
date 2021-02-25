<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
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
        $section = new Section();
        $section->generalsheet_id = $request->generalsheet_id;
        $section->title = $request->title;
        $section->save();

        return redirect()->route('generalrequests.index', ['generalsheet_id' => $section->generalsheet_id,'section_id' =>$section->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        $section->title = $request->title;
        $section->update();

        return redirect()->route('generalrequests.index', ['generalsheet_id' => $section->generalsheet_id,'section_id' =>$section->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('generalrequests.index', ['generalsheet_id' => $section->generalsheet_id]);
    }
}
