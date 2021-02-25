<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Bench;
use CtoVmm\Sheet;
use CtoVmm\Cat;
use CtoVmm\Subcat;
use CtoVmm\Feature;
use CtoVmm\Unit;
use CtoVmm\Assessment;
use CtoVmm\Assessmenttype;
//Export to excel:
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Storage;

class BenchFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($bench_id,$sheet_id,$cat_id=0,$subcat_id=0)
    {
        $bench = Bench::find($bench_id);
        $sheet = Sheet::find($sheet_id);
        if ($cat_id==0)
            $cat = $sheet->cats()->first();
        else
            $cat = Cat::find($cat_id);
        if ($subcat_id==0)
            $subcat = $cat->subcats()->first();
        else
            $subcat = Subcat::find($subcat_id);
        $units = Unit::all()->keyBy('id');



        //Check status:
        $status_cats=array();
        $status_subcats=array();
        $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');
        foreach ($sheet->cats()->get() as $item_cat) {
            $status_cat = 2;
            foreach ($item_cat->subcats()->get() as $item_subcat) {
                $status_subcat=2;
                foreach ($item_subcat->questions()->get() as $question) {
                    foreach ($question->features()->get() as $item_feature) {
                        if ($bench_features[$item_feature->id]->pivot->status==1) {
                            $status_subcat=1;
                            $status_cat=1;
                        }
                    }
                }
                $status_subcats[$item_subcat->id]=$status_subcat;
            }
            $status_cats[$item_cat->id]=$status_cat;
        }
        return view('intranet.benchfeatures')
            ->with('bench',$bench)
            ->with('bench_features',$bench_features)
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat)
            ->with('status_cats',$status_cats)
            ->with('status_subcats',$status_subcats)
            ->with('units',$units);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $bench = Bench::find($request->bench_id);
        $bench->features()->updateExistingPivot($request->feature_id, ['value' => $request->value,'status' => 1, 'comments' => $request->comments]);
        $this->updateBenchState($bench,$request->sheet_id);
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function featureState($bench_id,$sheet_id,$cat_id,$subcat_id,$feature_id) {
        $bench = Bench::find($bench_id);
        $btsf = $bench->features()->where('feature_id', $feature_id)->first();
        $new_status = 2;
        if ($btsf->pivot->status == 2) $new_status = 1;
        //Check if question root:
        if ($btsf->question()->first()->questiontype_id==2 && $btsf->question_root==1) {
            //Update status of all other features of the group:
            $question = $btsf->question()->first();
            foreach ($question->features()->get() as $feature) {
                $bench->features()->updateExistingPivot($feature->id, ['status' => $new_status]);
            }
        } else
            $bench->features()->updateExistingPivot($feature_id, ['status' => $new_status]);
        $this->updateBenchState($bench,$sheet_id);
        return redirect()->route('benchfeatures.index', ['bench' =>$bench_id,'sheet'=>$sheet_id,'cat' =>$cat_id,'subcat'=>$subcat_id]);
    }

    private function updateBenchState($bench,$sheet_id){
        $bench_status=2;
        $sheet_status=2;
        foreach ($bench->features()->get() as $feature) {
            if ($feature->pivot->status==1) $bench_status=1;
            if ($feature->question()->first()->subcat()->first()->cat()->first()->sheet_id == $sheet_id) {
                if ($feature->pivot->status==1) $sheet_status=1;
            }
        }
        //Update bench / techsheet status:
        $bench->sheets()->updateExistingPivot($sheet_id,['status' => $sheet_status]);
        //Update bench status:
        $bench->status = $bench_status;
        $bench->update();
    }

    public function attach(Request $request) {
        /*
        $request->validate([
            'attach_file' => 'required|file|max:2048',
        ]);
        */
        if ($request->hasFile('attach_file')) {
            $bench = Bench::find($request->bench_id);
            $feature = Feature::find($request->feature_id);
            $request->file('attach_file')->store('attachs','public_path');
            $filename = $request->file('attach_file')->hashName();
            $bench->featuresfiles()->attach($feature,["title"=>"$request->title","file"=>"$filename"]);
        }
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    public function detach(Request $request) {
        $bench = Bench::find($request->bench_id);
        $feature = Feature::find($request->feature_id);
        $file =$bench->featuresfiles()->wherePivot('id',$request->id)->first()->pivot->file;
        //Remove from DB:
        $bench->featuresfiles()->wherePivot('id',$request->id)->detach($feature->id);
        //Check if any other feature use this file or not (remove file):
        $num_items = $bench->featuresfiles()->wherePivot('file',$file)->count();
        if($num_items==0) {
            Storage::delete(public_path().'/attachs/'.$file);
        }
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    public function brandAdd(Request $request) {
        $bench = Bench::find($request->bench_id);
        $feature = Feature::find($request->feature_id);
        $bench->featuresbrands()->attach($feature,['brand_name'=>$request->brand_name,'brand_value'=>$request->brand_value]);
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    public function brandUpdate(Request $request) {
        $bench = Bench::find($request->bench_id);
        $bench->featuresbrands()->wherePivot('id',$request->id)->updateExistingPivot($request->feature_id,['brand_name'=>$request->brand_name,'brand_value'=>$request->brand_value]);
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    public function brandDelete(Request $request) {
        $bench = Bench::find($request->bench_id);
        $feature = Feature::find($request->feature_id);
        $bench->featuresbrands()->wherePivot('id',$request->id)->detach($feature->id);
        return redirect()->route('benchfeatures.index', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    //Reports
    public function Rep_BenchFeatures($bench_id,$sheet_id,$cat_id=0,$subcat_id=0)
    {
        $bench = Bench::find($bench_id);
        $sheet = Sheet::find($sheet_id);
        if ($cat_id==0)
            $cat = $sheet->cats()->first();
        else
            $cat = Cat::find($cat_id);
        if ($subcat_id==0)
            $subcat = $cat->subcats()->first();
        else
            $subcat = Subcat::find($subcat_id);
        $units = Unit::all()->keyBy('id');
        $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');

        return view('reports.benchfeatures')
            ->with('bench',$bench)
            ->with('bench_features',$bench_features)
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat)
            ->with('units',$units);
    }

    public function Rep_ParametersBenchFeatures($bench_id,$sheet_id,$cat_id=0,$subcat_id=0) {
        $bench = Bench::find($bench_id);
        $sheet = Sheet::find($sheet_id);
        if ($cat_id==0)
            $cat = $sheet->cats()->first();
        else
            $cat = Cat::find($cat_id);
        if ($subcat_id==0)
            $subcat = $cat->subcats()->first();
        else
            $subcat = Subcat::find($subcat_id);
        $units = Unit::all()->keyBy('id');
        $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');

        return view('reports.param_features')
            ->with('bench',$bench)
            ->with('bench_features',$bench_features)
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat)
            ->with('units',$units);
    }

    public function Rep_ParametersBenchFeatureUpdate(Request $request) {
        $bench = Bench::find($request->bench_id);
        if (strlen($request->value)==0)
            $bench->features()->updateExistingPivot($request->feature_id, ['value' => null]);
        else
            $bench->features()->updateExistingPivot($request->feature_id, ['value' => $request->value,]);
        return redirect()->route('benches.reports.parameters.features', ['bench' =>$bench->id,'sheet'=>$request->sheet_id,'cat' =>$request->cat_id,'subcat'=>$request->subcat_id]);
    }

    public function Rep_ParametersShowFeatures($bench_id,$sheet_id,$cat_id=0,$subcat_id=0) {
        $bench = Bench::find($bench_id);
        $sheet = Sheet::find($sheet_id);

        if ($cat_id==0)
            $cat = $sheet->cats()->first();
        else
            $cat = Cat::find($cat_id);
        if ($subcat_id==0)
            $subcat = $cat->subcats()->first();
        else
            $subcat = Subcat::find($subcat_id);
        //Get benches asociated to this area-component (filter by specific technical sheet features)
        $benches = Bench::where([
            ['area_component_id',$bench->area_component_id],
            ['benchtype_id','1']
        ])->get();
        $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');
        return view('reports.param_show_features')
            ->with('benches',$benches)
            ->with('bench',$bench)
            ->with('bench_features',$bench_features)
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat);
    }

    public function Exc_ParametersShowFeatures($bench_id,$sheet_id=0,$cat_id=0,$subcat_id=0) {
        $ass_type = Assessmenttype::where('key','tech')->first();
        $bench = Bench::find($bench_id);
        ob_end_clean();
        ob_start();
        $bench_file = "Rep_Parameters_".$bench_id."_".$ass_type->key."_".$bench->title;
        Excel::create($bench_file, function ($excel) use ($bench,$ass_type,$sheet_id,$cat_id,$subcat_id) {
            $component = $bench->areaComponent()->first()->component()->first();
            //Bench info:
            $excel->sheet("BENCH", function($sheet) use ($bench,$component,$ass_type,$sheet_id,$cat_id,$subcat_id) {
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  11
                    )
                ));
                //Bench general info:
                $areas=null;
                foreach ($component->areas()->get() as $area) {
                    if ($areas==null) 
                        $areas=$area->title;
                    else
                        $areas.="-".$area->title;
                }
                $sheet->setWidth('A', 20);
                $sheet->setWidth('B', 80);
                $row=1;
                $sheet->row($row, array("BENCH:",$bench->title));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setBackground('#afffe0');
                });
                $row+=4;
                $sheet->row($row, array("ENTITY:",$bench->entity()->first()->title));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setBackground('#afffe0');
                });
                $row+=2;
                $sheet->row($row, array("AREA:",$areas));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setBackground('#afffe0');
                });
                $row+=2;
                $sheet->row($row, array("COMPONENT:",$component->title));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setBackground('#afffe0');
                });
                $row+=2;
                $sheet->row($row, array("COUNTRY:",$bench->country()->first()->title));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setBackground('#afffe0');
                });
                $row+=2;
                $sheet->row($row, array("COMMENTS:"));
                $sheet->cells("A$row:B$row", function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setBackground('#afffe0');
                });
                $row++;
                $sheet->row($row, array($bench->comments));
                /*
                $bench_info[] = [
                    'Bench' => $bench->title,
                    'Entity' => $bench->entity()->first()->title,
                    'Area' => $areas,
                    'Country' => $bench->country()->first()->title,
                    'Comments' => $bench->comments,
                ];
                $sheet->with($bench_info, null, 'A1', false, false);
                */
            });
            //Benches for this area/component:
            $benches = Bench::where([
                ['area_component_id',$bench->area_component_id],
                ['benchtype_id','1']
            ])->get();
            //Required bench features values:
            $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');
            $tech_cap = $component->sheet_id;
            $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->get()->sortBy("order");
            foreach ($assessments as $techass) {
                foreach ($techass->sheets as $sh) {
                    if (($sheet_id==0 && ($sh->required == 1 || $sh->id == $tech_cap)) || ($sheet_id==$sh->id)) {
                        $excel->sheet($sh->abbrev, function($sheet) use ($bench,$bench_features,$sh,$cat_id,$subcat_id,$benches) {
                            $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                            $sheet->setStyle(array(
                                'font' => array(
                                    'name'      =>  'Verdana',
                                    'size'      =>  9
                                )
                            ));
                            $row=1;
                            $sheet->setWidth('A', 50);
                            $sheet->setWidth('B', 50);
                            $sheet->setWidth('C', 15);
                            $sheet->setWidth('D', 15);
                            //Header info, Sheet title + Benches of comparitive:
                            $header = array();
                            $header[] = "SHEET:";
                            $header[] = $sh->title;
                            foreach ($benches as $bench) {
                                $header[] = $bench->title;
                                $header[] = "";
                            }
                            $sheet->row($row, $header);
                            $sheet->cells("A$row:B$row", function($cell) {
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setBackground('#afffe0');
                            });
                            foreach ($sh->cats()->get() as $tscat) {
                                if (($cat_id==0) || ($cat_id==$tscat->id)) {
                                    $row+=2;
                                    $sheet->row($row, array("CATEGORY:",$tscat->title));
                                    $sheet->cells("A$row:B$row", function($cell) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setBackground('#feffd9');
                                    });
                                    foreach($tscat->subcats()->get() as $tssubcat) {
                                        if (($subcat_id==0) || ($subcat_id==$tssubcat->id)) {
                                            $row+=2;
                                            $sheet->row($row, array("SUBCATEGORY:",$tssubcat->title));
                                            $sheet->cells("A$row:B$row", function($cell) {
                                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                $cell->setBackground('#fff0ff');
                                            });
                                            foreach($tssubcat->questions()->get() as $question) {
                                                foreach($question->features()->get() as $item_feature) {
                                                    $type = "";
                                                    if ($item_feature->responsetype_id==3 && $item_feature->unit_id>2) {
                                                        $type .= $item_feature->unit()->first()->title;
                                                    }
                                                    $value = $bench_features[$item_feature->id]->pivot->value;
                                                    switch ($item_feature->responsetype_id) {
                                                        case '1':
                                                            $row++;
                                                            $sheet->row($row,array("$item_feature->title",$value));
                                                            break;
                                                        case '2':
                                                            $cell_values = array();
                                                            $cell_values[] = $item_feature->title;
                                                            $row++;
                                                            if (strlen($value)>0) {
                                                                $cell_values[] = $value.$type;
                                                            } else {
                                                                $cell_values[] = "";
                                                            }
                                                            foreach ($benches as $bench) {
                                                                $bf=$bench->features()->wherePivot('feature_id',$item_feature->id)->first();
                                                                if (strlen($bf->pivot->value)>0) {
                                                                    $cell_values[] = $bf->pivot->value;
                                                                    if ($bf->pivot->value == $value)
                                                                        $cell_values[] = "OK";
                                                                    else
                                                                        $cell_values[] = "";
                                                                } else {
                                                                    $cell_values[] = "";
                                                                    $cell_values[] = "";
                                                                }
                                                            }
                                                            $sheet->row($row,$cell_values);
                                                            $sheet->cells("B$row:Z$row", function($cells) {
                                                                $cells->setAlignment('center');
                                                            });
                                                            break;
                                                        case '3':
                                                            $cell_values = array();
                                                            $cell_values[] = $item_feature->title;
                                                            $row++;
                                                            if (strlen($value)>0) {
                                                                $cell_values[] = $value.$type;
                                                            } else {
                                                                $cell_values[] = "";
                                                            }
                                                            foreach ($benches as $bench) {
                                                                $cell_value="";
                                                                $cell_percent="";
                                                                $bf=$bench->features()->wherePivot('feature_id',$item_feature->id)->first();
                                                                if (strlen($bf->pivot->value)>0) {
                                                                    $cell_value = $bf->pivot->value;
                                                                    $cell_percent = number_format(($bf->pivot->value/$value)*100,0);
                                                                    $cell_values[] = $cell_value.$type;
                                                                    $cell_values[] = $cell_percent."%";
                                                                } else {
                                                                    $cell_values[] = "";
                                                                    $cell_values[] = "";
                                                                }
                                                            }
                                                            $sheet->row($row,$cell_values);
                                                            $sheet->cells("B$row:Z$row", function($cells) {
                                                                $cells->setAlignment('right');
                                                            });
                                                            break;
                                                        case '4':
                                                            $row++;
                                                            $sheet->row($row,array("$item_feature->title",$value));
                                                            break;
                                                        case '5':
                                                            //Check if has items:
                                                            $item_brands = $bench->featuresbrands()->wherePivot('feature_id',$item_feature->id)->get();
                                                            if (count($item_brands)>0) {
                                                                $row++;
                                                                $brand_unit="";
                                                                if ($item_feature->brand_value_unit>2) {
                                                                    $brand_unit = $item_feature->brand_value_unit()->first()->title;
                                                                }
                                                                $resptype = "BRAND/NUMBER: $item_feature->brand_name / $item_feature->brand_value";
                                                                $sheet->row($row,array("$item_feature->title",$value,$item_feature->brand_name,$item_feature->brand_value));
                                                                $sheet->getStyle("A$row")->getAlignment()->setWrapText(true);
                                                                $sheet->cell("A$row", function($cell) {
                                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                                    $cell->setBackground('#f3feff');
                                                                });
                                                                $sheet->cell("B$row", function($cell) {
                                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                                });

                                                                $sheet->cell("C$row:D$row", function($cell) {
                                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                                    $cell->setBackground('#ffacac');
                                                                });
                                                                
                                                                foreach ($item_brands as $item_brand) {
                                                                    $row++;
                                                                    $sheet->row($row,array("","",$item_brand->pivot->brand_name,$item_brand->pivot->brand_value.$brand_unit));
                                                                    $sheet->cell("C$row:D$row", function($cell) {
                                                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                                    });
                                                                }
                                                            }
                                                            break;
                                                    }
                                                    if ($item_feature->responsetype_id<5) {
                                                        //$sheet->setHeight($row, 20);
                                                        $sheet->getStyle("A$row")->getAlignment()->setWrapText(true);
                                                        $sheet->cell("A$row", function($cell) {
                                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                            $cell->setBackground('#f3feff');
                                                        });
                                                        $sheet->cell("B$row", function($cell) {
                                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                        });
                                                    }
                                                    /*
                                                    $color = '#4881f5';
                                                    $sheet->cell("D$row", function($cell) {
                                                        $cell->setBackground('#d3edff');
                                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                        $cell->setFontColor($color);
                                                        $cell->setAlignment('center');
                                                    });
                                                    */
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }
        })->download('xlsx');
    }
}
