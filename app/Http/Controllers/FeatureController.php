<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Sheet;
use CtoVmm\Cat;
use CtoVmm\Subcat;
use CtoVmm\Question;
use CtoVmm\Feature;
use CtoVmm\Responsetype;
use CtoVmm\Unittype;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sheet_id,$cat_id=0,$subcat_id=0)
    {
        $sheet = Sheet::find($sheet_id);
        if ($cat_id==0)
            $cat = $sheet->cats()->first();
        else
            $cat = Cat::find($cat_id);
        if ($subcat_id==0)
            $subcat = $cat->subcats()->first();
        else
            $subcat = Subcat::find($subcat_id);
        $responsetypes = Responsetype::all();
        $unittypes = Unittype::orderBy("title")->get();
        return view('intranet.features')
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat)
            ->with('responsetypes',$responsetypes)
            ->with('unittypes',$unittypes);
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
        //Create question item, type single or group:
        if ($request->questiontype_id==2) {  //Add group type question:
            $question = new Question();
            $question->questiontype_id=2;
            $question->subcat_id=$request->subcat_id;
            $question->save();

            //Add the first feature (Y/N) to the group (question):
            $feature = new Feature();
            $feature->title=$request->title;
            $feature->help=$request->help;
            $feature->responsetype_id=2;    //Must be Y/N
            $feature->question_id = $question->id;
            $feature->question_root = 1;
            $feature->unit_id=1;
            $feature->save();
        } else { //Add feature, could be new single (with a new question) or in a group:
            $feature = new Feature();
            $feature->title=$request->title;
            $feature->help=$request->help;
            $feature->responsetype_id=$request->responsetype_id;
            if ($request->question_id>0) {
                //Add feature to a group
                $question = Question::find($request->question_id);
                $feature->question_root = 0;
            } else {
                //New feature single
                $question = new Question();
                $question->questiontype_id=1;
                $feature->question_root = 1;
                $question->subcat_id=$request->subcat_id;
                $question->save();
            }
            $feature->question_id = $question->id;
            //Check if it is importable and it is parameter:
            if ($request->has('importable')) 
                $feature->importable = 1;
            else
                $feature->importable = 0;
            if ($request->has('parameter')) 
                $feature->parameter = 1;
            else
                $feature->parameter = 0;
            //Default values:
            $feature->order=1;
            $feature->unit_id=1;
            switch ($request->responsetype_id) {
                case '2':
                case '3':   
                    if ($request->responsetype_id==3)// Numeric: //User selected Unit
                        $feature->unit_id=$request->unit_id;
                    //Check Rating tool req:
                    if ($request->rating_req==1) {
                        $feature->rating_req=1;
                        $feature->rating_crit=$request->rating_crit;
                        $feature->rating_func=$request->rating_func;
                    }
                    break;
                case '5':   // Item/Value
                    $feature->brand_name=$request->brand_name;
                    $feature->brand_value=$request->brand_value;
                    $feature->brand_value_unit=$request->unit_id;
                    break;
                default:
                    break;
            }
            $feature->save();
        }
        //Add new feature:
        //Add this new feature to all the existing benches that use this techsheet:
        $sheet = Sheet::find($feature->question()->first()->subcat()->first()->cat()->first()->sheet_id);
        foreach ($sheet->benches()->get() as $bench) {
            $bench->features()->attach($feature);
        }
        return redirect()->route('features.index', ['id' => $sheet->id,'cat' =>$question->subcat()->first()->cat_id,'subcat'=>$request->subcat_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function show(Feature $feature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function edit(Feature $feature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feature $feature)
    {
        $feature->title=$request->title;
        $feature->help=$request->help;
        if ($request->questiontype_id==1) { //Single feature:
             //Check if it is importable and it is parameter:
            if ($request->has('importable')) 
                $feature->importable = 1;
            else
                $feature->importable = 0;
            if ($request->has('parameter')) 
                $feature->parameter = 1;
            else
                $feature->parameter = 0;
            //Check response type:
            $feature->responsetype_id=$request->responsetype_id;
            $feature->unit_id=1;

            $feature->rating_req=0;
            $feature->rating_crit=1;
            $feature->rating_func=1;

            $feature->brand_name=null;
            $feature->brand_value=null;
            $feature->brand_value_unit=1;
            switch ($request->responsetype_id) {
                case '2':
                case '3':
                    if ($request->responsetype_id==3)// Numeric: //User selected Unit
                        $feature->unit_id=$request->unit_id;
                    //Check Rating tool req:
                    if ($request->rating_req==1) {
                        $feature->rating_req=1;
                        $feature->rating_crit=$request->rating_crit;
                        $feature->rating_func=$request->rating_func;
                    }
                    break;
                case '5':
                    $feature->brand_name=$request->brand_name;
                    $feature->brand_value=$request->brand_value;
                    $feature->brand_value_unit=$request->unit_id;
                    break;
                default:
                    break;
            }
        }
        $feature->update();
        //TODO: Can update this feature to all the existing benches that use this sheet???
        // if responsetype change, remove current values (comments and attached files??) from existing benches, and update state

        $cat = $feature->question()->first()->subcat()->first()->cat()->first();
        return redirect()->route('features.index', ['id' => $cat->sheet_id,'cat' =>$cat->id,'subcat'=>$request->subcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feature $feature)
    {
        $subcat = $feature->question()->first()->subcat()->first();
        if ($feature->question_root==1) { //Delete question and all features
            $feature->question()->first()->delete(); //Delete cascade all features in features and bench_feature, files, brands tables
        } else { //Delete only this feature:
            $feature->delete(); //Delete cascade in features and bench_feature, files, brands tables
        }
        return redirect()->route('features.index', ['id' => $subcat->cat()->first()->sheet->id,'cat' =>$subcat->cat_id,'subcat'=>$subcat->id]);
    }
}
