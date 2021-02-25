<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Assessmenttype;
use CtoVmm\Assessment;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function index(Assessmenttype $asstype)
    {
        $assessments = Assessment::where('assessmenttype_id',$asstype->id)->get();
        return view('intranet.assessments')
            ->with('asstype',$asstype)
            ->with('assessments',$assessments);
    }

    public function indexTech()
    {
        $asstype = Assessmenttype::where('key','tech')->first();
        return $this->index($asstype);
    }

    public function indexEcon()
    {
        $asstype = Assessmenttype::where('key','economic')->first();
        return $this->index($asstype);
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
        $assessment = new Assessment();
        $assessment->title = $request->title;
        $assessment->assessmenttype_id = $request->assessmenttype_id;
        $assessment->save();
        return $this->index($assessment->assessmenttype()->first());
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function show(Assessment $assessment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assessment $assessment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assessment $assessment)
    {
        $assessment->title = strtoupper($request->title);
        $assessment->update();
        return $this->index($assessment->assessmenttype()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assessment $assessment)
    {
        $asstype = $assessment->assessmenttype()->first();
        //TODO: Check if exist benches?
        $assessment->delete();
        return $this->index($asstype);
    }
}
