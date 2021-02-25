<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Assessment;
use CtoVmm\Sheet;
use CtoVmm\Cat;
use CtoVmm\Subcat;
use CtoVmm\Feature;
use CtoVmm\Bench;

use Illuminate\Support\Facades\Response;

class SheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $assessment = Assessment::find($id);
        return view('intranet.sheets')
            ->with('assessment',$assessment);
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
        $sheet = new Sheet();
        $sheet->title = $request->title;
        $sheet->abbrev = $request->abbrev;
        $sheet->required = $request->required;
        $sheet->assessment_id = $request->assessment_id;
        $sheet->save();
        //Create cat and subcat:
        $cat = new Cat();
        $cat->title = "GENERAL";
        $cat->sheet_id = $sheet->id;
        $cat->save();
        $subcat = new Subcat();
        $subcat->title="GENERAL";
        $subcat->cat_id = $cat->id;
        $subcat->save();

        //If sheet type is "Required" ($sheet->required==1), add this new sheet to all the existing benches:
        $benches = Bench::all();
        foreach ($benches as $bench) {
            $bench->sheets()->attach($sheet,['status'=>1]);
        }
        //If type is "Specific" can't add to any existing benches, first must be asociated to a component/Areas

        return redirect()->route('sheets.index', ['id' => $request->assessment_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Sheet  $sheet
     * @return \Illuminate\Http\Response
     */
    public function show(Sheet $sheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Sheet  $sheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Sheet $sheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Sheet  $sheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sheet $sheet)
    {
        $sheet->title = $request->title;
        $sheet->abbrev = $request->abbrev;
        $sheet->required = $request->required;
        $sheet->update();
        return redirect()->route('sheets.index', ['id' => $sheet->assessment_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Sheet  $sheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sheet $sheet)
    {
        $assessment_id = $sheet->assessment_id;
        //TODO: Check if exist benches?
        $sheet->delete();
        return redirect()->route('sheets.index', ['id' => $assessment_id]);
    }

    /*
    public function listFeatures(Request $request) {
        $sheet = Sheet::find($request->sheet_id);
        $cats = $sheet->cats()->get();
        dd(Response::json($cats));
        return Response::json($cats);
    }
    */
    
    public function listSheetCatsSubcats($sheet_id) {
        $sheet = Sheet::find($sheet_id);
        $cat_arr = array();
        //Response must be cat/subcats/features:
        $cats = $sheet->cats()->get();
        foreach ($cats as $cat) {
            $subcat_arr = array();
            $subcats= $cat->subcats()->get();
            foreach ($subcats as $subcat) {
                $subcat_arr[] = array("id"=>$subcat->id,"subcat"=>"$subcat->title");
            }
            $cat_arr[] = array("id"=>$cat->id,"cat"=>"$cat->title","subcats"=>$subcat_arr);
        }
        //dd($cat_arr);
        return Response::json($cat_arr);
    }

    public function listSubcatFeatures($subcat_id) {
        $subcat = Subcat::find($subcat_id);
        $list = array();
        foreach ($subcat->questions()->get() as $question) {
            $features = Feature::where('question_id','=',$question->id)
                ->where(function($q) {
                    $q->where('responsetype_id','=','3')
                      ->orWhere('responsetype_id','=','2')
                      ->orWhere('responsetype_id','=','4');
                })->get();
            foreach ($features as $feature) {
                if ($feature->responsetype_id==2)
                    $unit="Yes/No";
                else
                    $unit = $feature->unit()->first()->title;
                $list[] = array("id"=>$feature->id,"title"=>"$feature->title","unit"=>"($unit)");
            }
        }
        //dd($list);
        return Response::json($list);
    }

}
