<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Bench;
use CtoVmm\Entity;
use CtoVmm\Component;
use CtoVmm\AreaComponent;
use CtoVmm\Customer;
use CtoVmm\Platform;
use CtoVmm\Product;
use CtoVmm\Occupation;
use CtoVmm\Occupationweek;
use CtoVmm\Weekstate;
use CtoVmm\ExcelColumn;
//Export to excel:
use Maatwebsite\Excel\Facades\Excel;

class OccupationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($bench_id,$year=0)
    {
        $bench = Bench::find($bench_id);
        //Only SGRE Platforms (to add new occupations with SGRE products):
        $cust_sgre = Customer::where('title','SGRE')->first();
        $platforms = Platform::where('customer_id',$cust_sgre->id)->get();

        $occyears = Occupation::where('bench_id','=',$bench->id)->orderBy('year','asc')->distinct()->get(['year']);
        if ($year==0) $year=date("Y");
        $weekstates = Weekstate::all();
        return view('intranet.occupation')
            ->with('bench',$bench)
            ->with('platforms',$platforms)
            ->with('occyears',$occyears)
            ->with('weekstates',$weekstates)
            ->with('current_year',$year);
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
        //Add product occupation to a bench in a year:
        $occ = new Occupation();
        $occ->bench_id = $request->bench_id;
        $occ->year =  $request->year;
        //Check if existing product or new:
        if ($request->product_opt==1) {
            //Existing:
            $occ->product_id = $request->product_id;
        } else {
            //Add new product:
            $product = new Product();
            $product->platform_id = $request->platform_id;
            $product->title = $request->product_name;
            $product->save();
            $occ->product_id = $product->id;
        }
        $occ->save();
        for($w=1;$w<=52;$w++){
            //All weeks free
            $occweek = Occupationweek::create(['occupation_id' => $occ->id,'week' => $w,'weekstate_id'=>1]);
        }
        return redirect()->route('occupation.index',['bench'=>$request->bench_id,'year'=>$request->year]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function show(Occupation $occupation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function edit(Occupation $occupation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Occupation $occupation)
    {
        //Update the occupation state for a product in a bench/year:
        for($week=$request->occ_week_from;$week<=$request->occ_week_to;$week++) {
            $occ = Occupationweek::where('occupation_id',$occupation->id)->where('week',$week)->first();
            $occ->weekstate_id = $request->occ_week_state;
            $occ->update();
        }
        return redirect()->route('occupation.index',['bench'=>$occupation->bench_id,'year'=>$occupation->year]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Occupation $occupation)
    {
        $bench_id = $occupation->bench_id;
        $year = $occupation->year;
        $occupation->delete();
        return redirect()->route('occupation.index',['bench'=>$bench_id,'year'=>$year]);
    }

    public function addYear(Request $request){
        //Create occupation for new year (Only for CUSTOMER and Other product):
        $product = Customer::where('title','CUSTOMER')->first()->platforms()->first()->products()->first();
        //Get from BD current last year for the bench
        $last_year = Occupation::where('bench_id',$request->bench_id)->max('year');

        $occ = new Occupation();
        $occ->bench_id = $request->bench_id;
        $occ->year = $last_year+1;
        $occ->product_id = $product->id;
        $occ->save();

        for($w=1;$w<=52;$w++){
            //All weeks free
            $occweek = Occupationweek::create(['occupation_id' => $occ->id,'week' => $w,'weekstate_id'=>1]);
        }
        return redirect()->route('occupation.index',['bench'=>$request->bench_id,'year'=>$occ->year]);
    }
    public function deleteYear(Request $request){
        //Only can delete if year > current year, and it is the last year of the bench occupation:
        $current_year = date("Y");
        //Get from BD current last year for the bench occupation
        $last_year = Occupation::where('bench_id',$request->bench_id)->max('year');
        if (($last_year == $request->year) && ($last_year>$current_year)) {
            $occupations = Occupation::where('bench_id',$request->bench_id)->where('year',$last_year)->get();
            foreach ($occupations as $occupation) {
                $occupation->delete();
            }
        }
        return redirect()->route('occupation.index',['bench'=>$request->bench_id,'year'=>0]);
    }

    //Reports:
    public function Rep_OccupationComponent(Request $request) {
        $component_id = 0;
        $year = 0;
        $week_from = 0;
        $week_to = 0;
        $benches=array();
        if ($request->component_id) {
            $component_id = $request->component_id;
            $year = $request->year;
            $week_from = $request->week_from;
            $week_to = $request->week_to;
            $benches = $this->getBenches_forComponent($component_id);
        }
        $components = Component::orderBy('title')->get();
        $weekstates = Weekstate::all();
        return view('reports.occupationcomponent')
            ->with('component_id',$component_id)
            ->with('year',$year)
            ->with('week_from',$week_from)
            ->with('week_to',$week_to)
            ->with('components',$components)
            ->with('benches',$benches)
            ->with('weekstates',$weekstates);
    }

    private function getBenches_forComponent($component_id) {
        $benches = array();
        //Get all the AreaComponents asociated with the component:
        $acs = AreaComponent::where('component_id',$component_id)->get();
        //Get all benches for this component:
        foreach ($acs as $ac) {
            foreach(Bench::where([
                        ['area_component_id',$ac->id],
                        ['benchtype_id','1']
                    ])->get() as $bench) {
                $benches[] = $bench;
            }
        }
        return $benches;
    }

    public function Rep_OccupationEntity(Request $request){
        $entity_id = 0;
        $year = 0;
        $benches=array();
        if ($request->entity_id) {
            $entity_id = $request->entity_id;
            $year = $request->year;
            //Get all the benches asociated to this entity:
            $benches = Bench::where([
                ['entity_id',$entity_id],
                ['benchtype_id','1']
            ])->get();

        }
        $entities = Entity::all();
        $components = Component::orderBy('title')->get();
        $weekstates = Weekstate::all();
        return view('reports.occupationentity')
            ->with('entity_id',$entity_id)
            ->with('year',$year)
            ->with('entities',$entities)
            ->with('components',$components)
            ->with('benches',$benches)
            ->with('weekstates',$weekstates);
    }
    public function Exc_occupationExport($bench_id) {
        $bench = Bench::find($bench_id);
        ob_end_clean();
        ob_start();
        $bench_file = $bench->title."_Occupation";
        Excel::create($bench_file, function ($excel) use ($bench) {
            $excel->sheet("OCCUPATION", function($sheet) use ($bench) {
                $sheet->setAutoSize(true);
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  9
                    )
                ));
                $columns=ExcelColumn::all()->keyBy('id');
                $sheet->cell("A1", function($cell){
                    $cell->setValue("BENCH:");
                });
                $sheet->cell("B1", function($cell) use($bench){
                    $cell->setValue($bench->title);
                });

                $sheet->cell("A2", function($cell){
                    $cell->setValue("ENTITY:");
                });
                $sheet->cell("B2", function($cell) use($bench){
                    $cell->setValue($bench->entity()->first()->title);
                });

                $sheet->cell("A3", function($cell){
                    $cell->setValue("AREA:");
                });
                $sheet->cell("B3", function($cell) use($bench){
                    $cell->setValue($bench->areaComponent()->first()->area()->first()->title);
                });

                $sheet->cell("A4", function($cell){
                    $cell->setValue("COMPONENT:");
                });
                $sheet->cell("B4", function($cell) use($bench){
                    $cell->setValue($bench->areaComponent()->first()->component()->first()->title);
                });

                $row=0;
                $week_states = Weekstate::all();
                foreach($week_states as $week_state) {
                    $row++;
                    $sheet->cell("M$row", function($cell) use($week_state){
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($week_state->id>1)
                            $cell->setBackground($this->weekStateColor($week_state->id));
                    });
                    $sheet->cell("N$row", function($cell) use($week_state){
                        $cell->setValue($week_state->title);
                    });
                }

                $row+=2;
                $occ_years = $bench->occupations()->orderBy('year','asc')->distinct()->get(['year']);
                foreach($occ_years as $occ_year) {
                    $occ_products = $bench->occupations()->where('year','=',$occ_year->year)->get();
                    $weeks = array();
                    $weeks[]="Year: ".$occ_year->year;
                    for ($w=1;$w<=52;$w++) {
                        $weeks[]=$w;
                    }
                    $sheet->row($row, $weeks);

                    foreach($occ_products as $occ_product) {
                        $row++;
                        $col=1;
                        $cell_pos = $columns[$col]->title.$row;
                        $product = $occ_product->product()->first()->platform()->first()->title.": ".$occ_product->product()->first()->title;
                        $sheet->cell($cell_pos, function($cell) use($product){
                            $cell->setValue($product);
                        });
                        foreach($occ_product->occupationweeks()->get() as $week_product) {
                            $col++;
                            $cell_pos = $columns[$col]->title.$row;
                            $sheet->setSize($cell_pos, 3, 15);
                            $state = $this->weekStateColor($week_product->weekstate_id);
                            $sheet->cell($cell_pos, function($cell) use ($state) {
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                if(strlen($state)>0) $cell->setBackground($state);
                            });
                        }
                    }
                    $row+=2;
                }
            });
        })->download('xlsx');
    }

    public function Exc_OccupationComponent($component_id,$year,$week_from,$week_to) {
        $benches = $this->getBenches_forComponent($component_id);
        ob_end_clean();
        ob_start();
        $bench_file = "OccupationByComponent";
        Excel::create($bench_file, function ($excel) use ($component_id,$year,$week_from,$week_to,$benches) {
            $excel->sheet("OCCUPATION", function($sheet) use ($component_id,$year,$week_from,$week_to,$benches) {
                $sheet->setAutoSize(true);
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  9
                    )
                ));
                $columns=ExcelColumn::all()->keyBy('id');
                $week_states = Weekstate::all();
                $component = Component::find($component_id);
                $row=1;
                //Print general info:
                $sheet->cell("A$row", function($cell){
                    $cell->setValue("REPORT:");
                });
                $sheet->cell("B$row", function($cell){
                    $cell->setValue("Occupation by component");
                });
                $row+=2;
                $sheet->cell("A$row", function($cell){
                    $cell->setValue("COMPONENT:");
                });
                $sheet->cell("B$row", function($cell) use($component){
                    $cell->setValue($component->title);
                });
                $row++;
                $sheet->cell("A$row", function($cell) use ($year){
                    $cell->setValue("YEAR ".$year);
                });
                $row++;
                $sheet->cell("A$row", function($cell) use($week_from, $week_to){
                    $cell->setValue("From week $week_from to $week_to");
                });
                //Print occupation colors:
                $row=0;
                foreach($week_states as $week_state) {
                    $row++;
                    $sheet->cell("Y$row", function($cell) use($week_state){
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($week_state->id>1)
                            $cell->setBackground($this->weekStateColor($week_state->id));
                    });
                    $sheet->cell("Z$row", function($cell) use($week_state){
                        $cell->setValue($week_state->title);
                    });
                }
                //For required weeks:
                $row++;
                $sheet->cell("Y$row", function($cell){
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                    $cell->setValue("x");
                });
                $sheet->cell("Z$row", function($cell) use($week_state){
                    $cell->setValue("Required");
                });

                //Occupation benches:
                $row++;
                foreach ($benches as $bench) {
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("BENCH:");
                        $cell->setFontColor("#0039dd");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->title);
                        $cell->setFontColor("#0039dd");
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("ENTITY:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->entity()->first()->title);
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("AREA:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->areaComponent()->first()->area()->first()->title);
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("COMPONENT:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->areaComponent()->first()->component()->first()->title);
                    });
                    $row++;
                    //By the momment, only selected year:
                    //$occ_years = $bench->occupations()->orderBy('year','asc')->distinct()->get(['year']);
                    $occ_years = $bench->occupations()->where('year',$year)->distinct()->get(['year']);
                    foreach($occ_years as $occ_year) {
                        $occ_products = $bench->occupations()->where('year','=',$occ_year->year)->get();
                        $weeks = array();
                        $weeks[]="Weeks:";
                        for ($w=1;$w<=52;$w++) {
                            $weeks[]=$w;
                        }
                        $sheet->row($row, $weeks);
                        $sheet->cells("B$row:BA$row", function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(8);
                        });
                        //Required weeks:
                        $range_required = $columns[$week_from+1]->title.$row.":".$columns[$week_to+1]->title.$row;
                        $sheet->cells($range_required, function($cells) {
                            $cells->setBackground("#cccccc");
                        });
                        //Product occupation:
                        foreach($occ_products as $occ_product) {
                            $row++;
                            $col=1;
                            $cell_pos = $columns[$col]->title.$row;
                            $product = $occ_product->product()->first()->platform()->first()->title.": ".$occ_product->product()->first()->title;
                            $sheet->cell($cell_pos, function($cell) use($product){
                                $cell->setValue($product);
                            });
                            foreach($occ_product->occupationweeks()->get() as $week_product) {
                                $col++;
                                $cell_pos = $columns[$col]->title.$row;
                                $sheet->setSize($cell_pos, 3, 15);
                                $state = $this->weekStateColor($week_product->weekstate_id);
                                $week = $week_product->week;
                                $sheet->cell($cell_pos, function($cell) use ($state,$week,$week_from,$week_to) {
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    if(strlen($state)>0) $cell->setBackground($state);
                                    if ($week>=$week_from && $week<=$week_to) {
                                        $cell->setAlignment('center');
                                        $cell->setValue("x");
                                    }
                                });
                            }
                        }
                        $row+=3;
                    }
                }
            });
        })->download('xlsx');
    }

    public function Exc_OccupationEntity($entity_id,$year) {
        $benches = Bench::where([['entity_id',$entity_id],['benchtype_id',1]])->get();
        ob_end_clean();
        ob_start();
        $bench_file = "OccupationByEntity";
        Excel::create($bench_file, function ($excel) use ($entity_id,$year,$benches) {
            $excel->sheet("OCCUPATION", function($sheet) use ($entity_id,$year,$benches) {
                $sheet->setAutoSize(true);
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  9
                    )
                ));
                $entity = Entity::find($entity_id);
                $columns=ExcelColumn::all()->keyBy('id');
                $week_states = Weekstate::all();
                $row=1;
                //Print general info:
                $sheet->cell("A$row", function($cell){
                    $cell->setValue("REPORT:");
                });
                $sheet->cell("B$row", function($cell){
                    $cell->setValue("Occupation by entity");
                });
                $row+=2;
                $sheet->cell("A$row", function($cell){
                    $cell->setValue("ENTITY:");
                });
                $sheet->cell("B$row", function($cell) use($entity){
                    $cell->setValue($entity->title);
                });
                $row++;
                $sheet->cell("A$row", function($cell) use ($year){
                    $cell->setValue("YEAR ".$year);
                });
                
                //Print occupation colors:
                $row=0;
                foreach($week_states as $week_state) {
                    $row++;
                    $sheet->cell("Y$row", function($cell) use($week_state){
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        if ($week_state->id>1)
                            $cell->setBackground($this->weekStateColor($week_state->id));
                    });
                    $sheet->cell("Z$row", function($cell) use($week_state){
                        $cell->setValue($week_state->title);
                    });
                }
                //Occupation benches:
                $row++;
                foreach ($benches as $bench) {
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("BENCH:");
                        $cell->setFontColor("#0039dd");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->title);
                        $cell->setFontColor("#0039dd");
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("ENTITY:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->entity()->first()->title);
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("AREA:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->areaComponent()->first()->area()->first()->title);
                    });
                    $row++;
                    $sheet->cell("A$row", function($cell){
                        $cell->setValue("COMPONENT:");
                    });
                    $sheet->cell("B$row", function($cell) use($bench){
                        $cell->setValue($bench->areaComponent()->first()->component()->first()->title);
                    });
                    $row++;
                    //By the momment, only selected year:
                    //$occ_years = $bench->occupations()->orderBy('year','asc')->distinct()->get(['year']);
                    $occ_years = $bench->occupations()->where('year',$year)->distinct()->get(['year']);
                    foreach($occ_years as $occ_year) {
                        $occ_products = $bench->occupations()->where('year','=',$occ_year->year)->get();
                        $weeks = array();
                        $weeks[]="Weeks:";
                        for ($w=1;$w<=52;$w++) {
                            $weeks[]=$w;
                        }
                        $sheet->row($row, $weeks);
                        $sheet->cells("B$row:BA$row", function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(8);
                        });

                        //Product occupation:
                        foreach($occ_products as $occ_product) {
                            $row++;
                            $col=1;
                            $cell_pos = $columns[$col]->title.$row;
                            $product = $occ_product->product()->first()->platform()->first()->title.": ".$occ_product->product()->first()->title;
                            $sheet->cell($cell_pos, function($cell) use($product){
                                $cell->setValue($product);
                            });
                            foreach($occ_product->occupationweeks()->get() as $week_product) {
                                $col++;
                                $cell_pos = $columns[$col]->title.$row;
                                $sheet->setSize($cell_pos, 3, 15);
                                $state = $this->weekStateColor($week_product->weekstate_id);
                                $sheet->cell($cell_pos, function($cell) use ($state) {
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    if(strlen($state)>0) $cell->setBackground($state);
                                });
                            }
                        }
                        $row+=3;
                    }
                }
            });
        })->download('xlsx');
    }

    private function weekStateColor($state_id) {
        $state ="";
        switch ($state_id) {
            case 2:
                $state ="#00ff00";
                break;
            case 3:
                $state ="#fffc00";
                break;
            case 4:
                $state ="#ffa841";
                break;
            case 5:
                $state ="#d30000";
                break;
            case 6:
                $state ="#5ad0e1";
                break;
            default:
                $state ="";
                break;
        }
        return $state;
    }
}
