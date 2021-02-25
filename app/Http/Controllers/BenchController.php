<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Bench;
use CtoVmm\Entity;
use CtoVmm\Area;
use CtoVmm\Component;
use CtoVmm\AreaComponent;
use CtoVmm\Assessment;
use CtoVmm\Assessmenttype;
use CtoVmm\Sheet;
use CtoVmm\Cat;
use CtoVmm\Subcat;
use CtoVmm\Feature;
use CtoVmm\Customer;
use CtoVmm\Occupation;
use CtoVmm\Occupationweek;
use CtoVmm\Weekstate;
use CtoVmm\Country;
//Export to excel:
use Maatwebsite\Excel\Facades\Excel;

class BenchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $benches = Bench::where('benchtype_id','1')->get();
        $entities = Entity::orderBy('title')->get();
        $areas = Area::orderBy('title')->get();
        $components = Component::orderBy('title')->get();
        $countries = Country::orderBy('title')->get();
        return view('intranet.benches')
            ->with('benches',$benches)
            ->with('entities',$entities)
            ->with('areas',$areas)
            ->with('countries',$countries)
            ->with('components',$components);
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
        //Same function for a new (empty) bench or from a existing bench ($request->bench_id>0):
        $b = new Bench();
        $b->title = $request->title;
        $b->comments = $request->comments;
        if ($request->bench_id>0) {
            $area_id = $request->area;
            $entity_id = $request->entity;
        } else {
            $area_id = $request->area_id;
            $entity_id = $request->entity_id;
        }
        $b->entity_id = $entity_id;
        $b->country_id = $request->country_id;
        $ac = AreaComponent::where([
            ['area_id','=',$area_id],
            ['component_id','=',$request->component_id]
        ])->first();
        $b->area_component_id = $ac->id;
        $b->save();
        
        //Attach specific sheet (required==2)
        $component = Component::find($request->component_id);
        $sheet = $component->sheet()->first();
        if ($request->bench_id>0) 
            $b->sheets()->attach($sheet,['status'=>1]);
        else
            $b->sheets()->attach($sheet);
        foreach($sheet->cats()->get() as $cat) {
            foreach($cat->subcats()->get() as $subcat) {
                foreach ($subcat->questions()->get() as $question) {
                    foreach ($question->features()->get() as $feature) {
                        $b->features()->attach($feature);
                    }
                }
            }
        }
        //Attach commun sheets (required==1)
        $sheets = Sheet::where('required','=','1')->get();
        foreach($sheets as $sheet) {
            if ($request->bench_id>0) 
                $b->sheets()->attach($sheet,['status'=>1]);
            else
                $b->sheets()->attach($sheet);
            foreach($sheet->cats()->get() as $cat) {
                foreach($cat->subcats()->get() as $subcat) {
                    foreach ($subcat->questions()->get() as $question) {
                        foreach ($question->features()->get() as $feature) {
                            $b->features()->attach($feature);
                        }
                    }
                }
            }
        }
        //Check if new (empty) or from existing bench
        if ($request->bench_id>0) {
            $bench_from = Bench::find($request->bench_id);
            //Copy the values of the common features:
            foreach ($bench_from->features()->get() as $feature_from ) {
                $b->features()->updateExistingPivot($feature_from->id, ['value' => $feature_from->pivot->value,'status' => 1, 'comments' => $feature_from->pivot->comments]);
            }
            //Copy attachments:
            foreach ($bench_from->featuresfiles()->get() as $feature_from) {
                $b->featuresfiles()->attach($feature_from->pivot->feature_id,['title'=>$feature_from->pivot->title,'file'=>$feature_from->pivot->file]);
            }
            //Copy brand items:
            foreach ($bench_from->featuresbrands()->get() as $feature_from) {
                $b->featuresbrands()->attach($feature_from->pivot->feature_id,['brand_name'=>$feature_from->pivot->brand_name,'brand_value'=>$feature_from->pivot->brand_value]);
            }
        }
        //TODO: Update status of the sheet not required (could be the same or not) ¿¿¿¿ use TRAIT ??????
        //function updateBenchStatus($bench,$sheet_id) from FeatureController

        //Create occupation for current year (Only for CUSTOMER and Other product):
        $product = Customer::where('title','CUSTOMER')->first()->platforms()->first()->products()->first();
        $occ = new Occupation();
        $occ->bench_id = $b->id;
        $occ->year = date("Y");
        $occ->product_id = $product->id;
        $occ->save();
        for($w=1;$w<=52;$w++){
            //All weeks free
            $occweek = Occupationweek::create(['occupation_id' => $occ->id,'week' => $w,'weekstate_id'=>1]);
        }
        return redirect()->route('benches.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Bench  $bench
     * @return \Illuminate\Http\Response
     */
    public function show(Bench $bench)
    {
        
    }

    public function showTech(Bench $bench, $imported=false)
    {
        $ass_type = Assessmenttype::where('key','tech')->first();
        $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->orderBy('order', 'asc')->get();
        return view('intranet.bench')
            ->with('assessments',$assessments)
            ->with('ass_type',$ass_type)
            ->with('imported',$imported)
            ->with('bench',$bench);
    }

    public function showEcon(Bench $bench, $imported=false)
    {
        $ass_type = Assessmenttype::where('key','economic')->first();
        $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->orderBy('order', 'asc')->get();
        return view('intranet.bench')
            ->with('assessments',$assessments)
            ->with('ass_type',$ass_type)
            ->with('imported',$imported)
            ->with('bench',$bench);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Bench  $bench
     * @return \Illuminate\Http\Response
     */
    public function edit(Bench $bench)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Bench  $bench
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bench $bench)
    {
        $bench->title = $request->title;
        $bench->comments = $request->comments;
        $bench->entity_id = $request->entity_id;
        $bench->country_id = $request->country_id;
        $bench->update();
        return redirect()->route('benches.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Bench  $bench
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bench $bench)
    {
        $bench->delete();
        return redirect()->route('benches.index');
    }

    public function Rep_Benches()
    {
        $benches = Bench::where('benchtype_id','1')->get();
        $entities = Entity::orderBy('title')->get();
        $areas = Area::orderBy('title')->get();
        $components = Component::orderBy('title')->get();
        return view('reports.benches')
            ->with('benches',$benches)
            ->with('entities',$entities)
            ->with('areas',$areas)
            ->with('components',$components);
    }

    public function Rep_showTech(Bench $bench)
    {
        $ass_type = Assessmenttype::where('key','tech')->first();
        $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->orderBy('order', 'asc')->get();
        return view('reports.bench')
            ->with('assessments',$assessments)
            ->with('ass_type',$ass_type)
            ->with('bench',$bench);
    }

    public function Rep_showEcon(Bench $bench)
    {
        $ass_type = Assessmenttype::where('key','economic')->first();
        $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->orderBy('order', 'asc')->get();
        return view('reports.bench')
            ->with('assessments',$assessments)
            ->with('ass_type',$ass_type)
            ->with('bench',$bench);
    }

    public function Rep_Occupation($bench_id,$year=0){
        $bench = Bench::find($bench_id);
        $occyears = Occupation::where('bench_id','=',$bench->id)->distinct()->get(['year']);
        if ($year==0) $year=date("Y");
        $weekstates = Weekstate::all();
        return view('reports.benchoccupation')
            ->with('bench',$bench)
            ->with('occyears',$occyears)
            ->with('weekstates',$weekstates)
            ->with('current_year',$year);
    }

    public function Rep_EntitiesTechSheet(Request $request)
    {
        $items=array();
        if ($request->area_id) {
            $area_id = $request->area_id;
            $components = Area::find($request->area_id)->components()->get()->sortBy('title');
            $component_id = $request->component_id;
            $ac = AreaComponent::where('area_id',$area_id)->where('component_id',$component_id)->first();
            if ($ac) $items = Bench::where([
                ['area_component_id',$ac->id],
                ['benchtype_id','1']
            ])->get();
        } else {
            $area_id = 0;
            $component_id = 0;
            $items = array();
            $components = array();
        }

        $entities = Entity::orderBy('title')->get();
        $areas = Area::orderBy('title')->get();

        return view('reports.entitiesbytechsheet')
            ->with('items',$items)
            ->with('entities',$entities)
            ->with('areas',$areas)
            ->with('components',$components)
            ->with('area_id',$area_id)
            ->with('component_id',$component_id);
    }

    public function Rep_ParametersFilter(Request $request) {
        $items=array();
        $sheet=null;
        $cat=null;
        $subcat=null;
        if ($request->area_id) {
            $area_id = $request->area_id;
            $components = Area::find($request->area_id)->components()->get()->sortBy('title');
            $component_id = $request->component_id;
            $ac = AreaComponent::where('area_id',$area_id)->where('component_id',$component_id)->first();
            if ($ac) {
                $sheet = $ac->component()->first()->sheet()->first();
                $cat = $sheet->cats()->first();
                $subcat = $cat->subcats()->first();
                //Get benches asociated to this area-component (filter by specific technical sheet features)
                $benches = Bench::where([
                    ['area_component_id',$ac->id],
                    ['benchtype_id','1']
                ])->get();
            } //else error? 
        } else {
            $area_id = 0;
            $component_id = 0;
            $benches = array();
            $components = array();
        }
        $areas = Area::orderBy('title')->get();
        return view('reports.parameters')
            ->with('benches',$benches)
            ->with('areas',$areas)
            ->with('components',$components)
            ->with('area_id',$area_id)
            ->with('component_id',$component_id)
            ->with('sheet',$sheet)
            ->with('cat',$cat)
            ->with('subcat',$subcat);
    }

    public function Rep_Parameters(){
        $benches = Bench::where('benchtype_id','2')->get();
        $areas = Area::orderBy('title')->get();
        $components = Component::orderBy('title')->get();
        return view('reports.param_benches')
            ->with('benches',$benches)
            ->with('areas',$areas)
            ->with('components',$components);
    }

    public function Rep_ParametersStore(Request $request) {
        $b = new Bench();
        $b->title = $request->title;
        $b->entity_id = 1;
        $b->country_id = 1;
        $b->comments = $request->comments;
        $b->benchtype_id=2;
        $ac = AreaComponent::where([
            ['area_id','=',$request->area_id],
            ['component_id','=',$request->component_id]
        ])->first();
        $b->area_component_id = $ac->id;
        $b->save();
        //Attach specific sheet (required==2)
        $component = Component::find($request->component_id);
        $sheet = $component->sheet()->first();
        $b->sheets()->attach($sheet);
        foreach($sheet->cats()->get() as $cat) {
            foreach($cat->subcats()->get() as $subcat) {
                foreach ($subcat->questions()->get() as $question) {
                    foreach ($question->features()->get() as $feature) {
                        $b->features()->attach($feature);
                    }
                }
            }
        }
        //Attach commun sheets (required==1)
        $sheets = Sheet::where('required','=','1')->get();
        foreach($sheets as $sheet) {
            $b->sheets()->attach($sheet);
            foreach($sheet->cats()->get() as $cat) {
                foreach($cat->subcats()->get() as $subcat) {
                    foreach ($subcat->questions()->get() as $question) {
                        foreach ($question->features()->get() as $feature) {
                            $b->features()->attach($feature);
                        }
                    }
                }
            }
        }
        return redirect()->route('benches.reports.parameters');
    }

    public function Rep_ParametersUpdate(Request $request, Bench $bench)
    {
        $bench->title = $request->title;
        $bench->comments = $request->comments;
        $bench->update();
        return redirect()->route('benches.reports.parameters');
    }

    public function Rep_ParametersDestroy(Bench $bench)
    {
        $bench->delete();
        return redirect()->route('benches.reports.parameters');
    }

    public function Rep_ParametersBench($id){
        $bench = Bench::find($id);
        //Only technical assessments:
        $assessments = Assessment::where('assessmenttype_id','1')->orderBy('order', 'asc')->get();
        return view('reports.param_bench')
            ->with('assessments',$assessments)
            ->with('bench',$bench);
    }

    public function Rep_ParametersShow($id) {
        $bench = Bench::find($id);
        //Only technical assessments:
        $assessments = Assessment::where('assessmenttype_id','1')->orderBy('order', 'asc')->get();
        return view('reports.param_show_bench')
            ->with('assessments',$assessments)
            ->with('bench',$bench);
    }

    public function benchExport($id) {
        $bench = Bench::find($id);
        ob_end_clean();
        ob_start();
        $bench_file = "BENCH_".$id."_".$bench->title;
        Excel::create($bench_file, function ($excel) use ($bench) {
            $component = $bench->areaComponent()->first()->component()->first();
            $tech_cap = $component->sheet_id;
            $techassessments = Assessment::all()->sortBy("order");
            foreach ($techassessments as $techass) {
                foreach ($techass->sheets as $sh) {
                    if ($sh->required == 1 || $sh->id == $tech_cap) {
                        $excel->sheet($sh->abbrev, function($sheet) use ($sh) {
                            $sheet->freezeFirstRow();
                            $row=1;
                            $sheet->row($row, array("ID","TYPE","FEATURE","RESPONSE","VALUE","BRAND","NUMBER","UNIT"));
                            $sheet->cells('A1:D1', function($cells) {
                                $cells->setBackground('#f0f0f0');
                            });
                            $sheet->cell('E1', function($cells) {
                                $cells->setBackground('#ccffc5');
                            });
                            $sheet->cells('F1:G1', function($cells) {
                                $cells->setBackground('#a6ebff');
                            });
                            $sheet->cell('H1', function($cells) {
                                $cells->setBackground('#f0f0f0');
                            });
                            foreach ($sh->cats()->get() as $tscat) {
                                $row++;
                                $sheet->row($row, array("","","$tscat->title"));
                                $sheet->cells("A$row:C$row", function($cell) {
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setBackground('#feffd9');
                                });
                                foreach($tscat->subcats()->get() as $tssubcat) {
                                    $row++;
                                    $sheet->row($row, array("","","$tssubcat->title"));
                                    $sheet->cells("A$row:C$row", function($cell) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setBackground('#fff0ff');
                                    });
                                    foreach($tssubcat->questions()->get() as $question) {
                                        foreach($question->features()->get() as $item_feature) {
                                            $row++;
                                            $type = $item_feature->responsetype()->first()->title;
                                            if ($item_feature->responsetype_id==3 && $item_feature->unit_id>2) {
                                                $type .= " (".$item_feature->unit()->first()->title.")";
                                            }
                                            if ($item_feature->responsetype_id==5) {
                                                $brand_unit="";
                                                if ($item_feature->brand_value_unit>2) {
                                                    $brand_unit = $item_feature->brand_value_unit()->first()->title;
                                                }
                                                $resptype = "BRAND/NUMBER: $item_feature->brand_name / $item_feature->brand_value";
                                                $sheet->row($row,array("$item_feature->id","$item_feature->responsetype_id","$item_feature->title","$resptype","","","","$brand_unit","(Insert more rows like this if necessary)"));
                                                $color = '#4881f5';
                                                $sheet->cell("F$row", function($cell) {
                                                    $cell->setBackground('#d3edff');
                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                });
                                                $sheet->cell("G$row", function($cell) {
                                                    $cell->setBackground('#d3edff');
                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                });
                                            } else {
                                                $sheet->row($row,array("$item_feature->id","$item_feature->responsetype_id","$item_feature->title","$type"));
                                                $color = '#0db700';
                                                $sheet->cell("E$row", function($cell) {
                                                    $cell->setBackground('#c9ffc5');
                                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                                });
                                            }
                                            $sheet->cell("D$row", function($cell) use ($color) {
                                                $cell->setFontColor($color);
                                            });
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

    public function benchImport(Request $request, $id) {
        $bench = Bench::find($id);
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            //check that the file corresponds to the bench to import:
            $bench_file_info = explode("_",$file->getClientOriginalName());
            if ($bench_file_info[1] == $bench->id) {
                $file->store('import','public_path');
                $filename = $file->hashName();
                //$path = asset('files/import/'.$filename);
                $path = public_path().'/import/'.$filename;
                Excel::load($path, function($reader) use($bench) {
                    $results = $reader->get();
                    //Remove all bench brand attachs:
                    $bench->featuresbrands()->detach();
                    $reader->each(function($sheet) use($bench){
                        //echo $sheet->getTitle()."<br/>";
                        $sheet->each(function($row) use($bench){
                            //echo $row->id."-".$row["feature"]."-".$row["value"]."<br/>";
                            $value="";
                            if ($row->id>0) {
                                switch ($row->type) {
                                    case '1':
                                        $value = $row->value;
                                        break;
                                    case '2':
                                        if (strlen($row->value)>0) {
                                            if (strtolower($row->value) == "y" || strtolower($row->value)=="yes") $value="Yes";
                                            if (strtolower($row->value) == "n" || strtolower($row->value)=="no") $value="No";
                                        }
                                        break;
                                    case '3':
                                        //Get number value:
                                        if (strlen($row->value)>0)
                                            $value = $this->getNumericValue($row->value);
                                        break;
                                    case '4':
                                        $value = $row->value;
                                        break;
                                    case '5':
                                        $brand_name="";
                                        if (strlen($row->brand)>0) {
                                            $brand_name=$row->brand;
                                        }
                                        if (strlen($row->number)>0) {
                                            $brand_value=$this->getNumericValue($row->number);
                                        }
                                        if (strlen($brand_name)>0) {
                                            $feature = Feature::find($row->id);
                                            $bench->featuresbrands()->attach($feature,['brand_name'=>$brand_name,'brand_value'=>$brand_value]);
                                        }
                                        break;
                                }
                            }
                            if (strlen($value)>0) {
                                $feature = Feature::find($row->id);
                                $bench->features()->updateExistingPivot($feature->id, ['value' => $value,'status' => 2, 'comments' => '']);
                            }
                        });
                    });
                });
                return redirect()->route('bench.assessments.technical',['bench'=>$bench->id,'imported'=>1]);
            } else {
                return redirect()->route('benches.index');
            }
        } else {
            return redirect()->route('benches.index');
        }
    }

    private function getNumericValue($val)
    {
        $val = str_replace(',', '.', $val);
        if (is_numeric($val)) {
            if (strpos($val,".")) {
                //Decimal value:
                return number_format($val,2);
            } else {
                //Integer value:
                return $val;
            }
        } else {
            return "";
        } 
    }

    //EXPORTS TO EXCEL:
    public function Exc_benchesExport() {
        $list = array();
        $benches = Bench::where('benchtype_id','1')->get();
        foreach ($benches as $bench) {
            $component = $bench->areaComponent()->first()->component()->first();
            $areas=null;
            foreach ($component->areas()->get() as $area) {
                if ($areas==null) 
                    $areas=$area->title;
                else
                    $areas.="-".$area->title;
            }
            $list[] = [
                '#' => $bench->id,
                'Bench' => $bench->title,
                'Comments' => $bench->comments,
                'Entity' => $bench->entity()->first()->title,
                'Area' => $areas,
                'Country' => $bench->country()->first()->title
            ];
        }
        ob_end_clean();
        ob_start();
        Excel::create('benches', function ($excel) use ($list) {
            $excel->sheet('Benches', function ($sheet) use ($list) {
                $sheet->with($list, null, 'A1', false, false);
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  10
                    )
                ));
            });
        })->download('xlsx');
    }

    public function Exc_EntitiesTechSheet($area_id,$component_id) {
        $benches = array();
        $components = Area::find($area_id)->components()->get()->sortBy('title');
        $ac = AreaComponent::where('area_id',$area_id)->where('component_id',$component_id)->first();
        if ($ac) $benches = Bench::where([
            ['area_component_id',$ac->id],
            ['benchtype_id','1']
        ])->get();
        foreach ($benches as $bench) {
            $component = $bench->areaComponent()->first()->component()->first();
            $areas=null;
            foreach ($component->areas()->get() as $area) {
                if ($areas==null) 
                    $areas=$area->title;
                else
                    $areas.="-".$area->title;
            }
            $list[] = [
                '#' => $bench->id,
                'Bench' => $bench->title,
                'Comments' => $bench->comments,
                'Entity' => $bench->entity()->first()->title,
                'Area' => $areas,
                'Country' => $bench->country()->first()->title
            ];
        }
        ob_end_clean();
        ob_start();
        Excel::create('RepEntitiesByTechSheet', function ($excel) use ($list) {
            $excel->sheet('Benches', function ($sheet) use ($list) {
                $sheet->with($list, null, 'A1', false, false);
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  10
                    )
                ));
            });
        })->download('xlsx');
    }

    public function Exc_benchAssTechExport($bench_id,$sheet_id=0,$cat_id=0,$subcat_id=0) {
        $ass_type = Assessmenttype::where('key','tech')->first();
        $this->Exc_benchAssExport($bench_id,$ass_type,$sheet_id,$cat_id,$subcat_id);
    }

    public function Exc_benchAssEconExport($bench_id,$sheet_id=0,$cat_id=0,$subcat_id=0) {
        $ass_type = Assessmenttype::where('key','economic')->first();
        $this->Exc_benchAssExport($bench_id,$ass_type,$sheet_id,$cat_id,$subcat_id);
    }

    private function Exc_benchAssExport($bench_id,$ass_type,$sheet_id,$cat_id,$subcat_id) {
        $bench = Bench::find($bench_id);
        ob_end_clean();
        ob_start();
        $bench_file = "BENCH_".$bench_id."_".$ass_type->key."_".$bench->title;
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
            $bench_features = $bench->features()->get()->keyBy('pivot.feature_id');
            $tech_cap = $component->sheet_id;
            $assessments = Assessment::where('assessmenttype_id',$ass_type->id)->get()->sortBy("order");
            foreach ($assessments as $techass) {
                foreach ($techass->sheets as $sh) {
                    if (($sheet_id==0 && ($sh->required == 1 || $sh->id == $tech_cap)) || ($sheet_id==$sh->id)) {
                        $excel->sheet($sh->abbrev, function($sheet) use ($bench,$bench_features,$sh,$cat_id,$subcat_id) {
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
                            $sheet->row($row, array("SHEET:",$sh->title));
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
                                                            $row++;
                                                            $sheet->row($row,array("$item_feature->title",$value));
                                                            $sheet->cell("B$row", function($cell) {
                                                                $cell->setAlignment('center');
                                                            });
                                                            break;
                                                        case '3':
                                                            $row++;
                                                            if (strlen($value)>0) $value.=$type;
                                                            $sheet->row($row,array("$item_feature->title",$value));
                                                            $sheet->cell("B$row", function($cell) {
                                                                $cell->setAlignment('right');
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