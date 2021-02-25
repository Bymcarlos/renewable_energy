<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Rating;
use CtoVmm\Bench;
use CtoVmm\Applicable;
use CtoVmm\Techcat;
use CtoVmm\Timecat;
use CtoVmm\Economiccat;
use CtoVmm\Criticality;
use CtoVmm\Criteriafunc;
use CtoVmm\Ratingtimerequest;
use Illuminate\Support\Facades\DB;
use Log;
use Exception;
use DateTime;

//Export to excel:
use CtoVmm\Ratingfile;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;

class ScoresController extends Controller {   
    private const RES_CAUTION=0;
    private const RES_GO=1;
    private const RES_NOGO=-1;

    protected $CUSTOM_ERROR=-1;
    private $page = "ratingtools.error";
    private $log_dev = false;

    //Common:
    private $m_rating;
    //Technical rating:
    private $m_ratinginputrequests;
    private $m_techcats;
    private $m_techcats_applicables;
    private $m_benches;
    private $m_criticalities;
    private $m_criteriafuncs;
    private $m_criticalities_total;
    private $m_techcats_scores;

    private $m_cat_type;
    //Timing rating:
    private $m_timing_availability;
    private $m_timing_executions;
    private $m_timing_flexibility;
    private $m_timing_finalscore;
    private $m_timing_cats;
    
    //Economics rating:
    private $m_economic_business;
    private $m_economic_alternative;
    private $m_economic_finalscore;
    private $m_economic_cats;

    //Export to excel technical rating score:
    public function Exc_technical($rating_id) {
        $this->m_rating = Rating::find($rating_id);
        $this->technicalRating();
        $tech_file = "RATING_Technical_".$rating_id."_".time();
        ob_end_clean();
        ob_start();
        $excel_file = Excel::create($tech_file, function ($excel) {
            //Input from TS:
            $this->createInputdataSheet($excel);
            $this->createTechnicalSheet($excel);
        })->download();
    }

    public function Exc_timing($rating_id) {
        $this->m_rating = Rating::find($rating_id);
        $this->timingRating();
        $tech_file = "RATING_Timing_".$rating_id."_".time();
        ob_end_clean();
        ob_start();
        $excel_file = Excel::create($tech_file, function ($excel) {
            $this->createTimingSheet($excel);
        })->download();
    }

    public function Exc_economics($rating_id) {
        $this->m_rating = Rating::find($rating_id);
        $this->economicsRating();
        $tech_file = "RATING_Economics_".$rating_id."_".time();
        ob_end_clean();
        ob_start();
        $excel_file = Excel::create($tech_file, function ($excel) {
            $this->createEconomicsSheet($excel);
        })->download();
    }

    private function createInputdataSheet($excel) {
        $excel->sheet("A-Input from TS", function($sheet) {
            $style_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $sheet->getColumnDimension("A")->setWidth(60);
            $sheet->getColumnDimension("B")->setWidth(500);
            $sheet->getColumnDimension("C")->setWidth(60);
            $inputsheet = $this->m_rating->techsheet()->first()->inputsheet()->first();
            $row=1;
            foreach ($inputsheet->inputcats()->get() as $inputcat) {
                $sheet->row($row, array($inputcat->id,$inputcat->title));
                $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                $sheet->getStyle("A$row:C$row")->getFont()->setSize(10);
                $row++;
                foreach ($inputcat->inputrequests()->get() as $inputrequest) {
                    $sheet->row($row, array($inputrequest->id,$inputrequest->title,$this->m_ratinginputrequests[$inputrequest->id]->value));
                    $sheet->getStyle("A$row:C$row")->getFont()->setSize(9);
                    $sheet->getStyle("C$row")->applyFromArray($style_center);
                    $row++;
                }
                $row++;
            }
        });
    }

    private function createTechnicalSheet($excel) {
        $excel->sheet("B-Technical Rating", function($sheet) {
        //COL=["","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35"];
        $COLS=["","A","B","C","D","E","F","G","H","I","J" ,"K" ,"L" ,"M" ,"N" ,"O" ,"P" ,"Q" ,"R" ,"S" ,"T" ,"U" ,"V" ,"W" ,"X" ,"Y" ,"Z" ,"AA","AB","AC","AD","AE","AF","AG","AH","AI"];
            try {
                //error_reporting(E_ALL);
                //$sheet->freezeFirstRow();
                $style_center = array(
                    'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $style_nogo = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FF0000')
                        )
                );
                $style_caution = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'F0FF00')
                        )
                );
                $style_go = array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '00FF00')
                        )
                );
                $criteriafuncs = Criteriafunc::all()->keyBy("id");
                $row=1;
                //Only applicable categories:
                foreach ($this->m_techcats_applicables as $id =>$data) {
                    $techcat = $this->m_techcats[$id];
                    $sheet->row($row, array("$techcat->id","$techcat->title"));
                    $row++;
                    $col=0;
                    $sheet->getColumnDimension($COLS[++$col])->setWidth(60);
                    $sheet->getColumnDimension($COLS[++$col])->setWidth(800);
                    $sheet->getColumnDimension($COLS[++$col])->setWidth(60);
                    $sheet->getColumnDimension($COLS[++$col])->setWidth(60);
                    $sheet->getColumnDimension($COLS[++$col])->setWidth(60);
                    $sheet->mergeCells("D$row:G$row"); //CRITICALITY
                    $sheet->getStyle("D$row:G$row")->applyFromArray($style_center);
                    $sheet->mergeCells("H$row:J$row");    //INPUT
                    $sheet->getStyle("H$row:J$row")->applyFromArray($style_center);
                    $sheet->mergeCells("K$row:L$row");    //CRITERIA
                    $sheet->getStyle("K$row:L$row")->applyFromArray($style_center);
                    //BENCHES
                    $col_ini=13;
                    foreach ($this->m_benches as $bench_id => $bench_data) {
                        $col_fin = $col_ini + 2;
                        $sheet->mergeCells("$COLS[$col_ini]$row:$COLS[$col_fin]$row");    
                        $sheet->getStyle("$COLS[$col_ini]$row:$COLS[$col_fin]$row")->applyFromArray($style_center);
                        $col_ini = $col_ini + 4;
                    }
                    //MAIN LABELS:
                    $row_labels=array();
                    $row_labels[]="ID";
                    $row_labels[]="REQUIREMENT";
                    $row_labels[]="FEATURE";
                    $row_labels[]="CRITICALITY";
                    $row_labels[]="";
                    $row_labels[]="";
                    $row_labels[]="";
                    $row_labels[]="INPUT TS - Factor";
                    $row_labels[]="";
                    $row_labels[]="";
                    $row_labels[]="CRITERIA";
                    $row_labels[]="";
                    foreach ($this->m_benches as $bench_id => $bench_data) {
                        $row_labels[]=$bench_data->title;
                        $row_labels[]="";
                        $row_labels[]="";
                        $row_labels[]="";
                    }
                    $sheet->row($row, $row_labels);
                    $sheet->getStyle("A$row:Z$row")->getFont()->setBold(true);
                    $sheet->getStyle("A$row:Z$row")->getFont()->setSize(10);
                    $row++;

                    //COLUMN LABELS:
                    $row_labels=array();
                    $row_labels[]="";
                    $row_labels[]="";
                    $row_labels[]="";
                    $row_labels[]="PRI";
                    $row_labels[]="SEC";
                    $row_labels[]="TER";
                    $row_labels[]="";
                    $row_labels[]="INPUT";
                    $row_labels[]="FACTOR";
                    $row_labels[]="TS*FACTOR";
                    $row_labels[]="ID";
                    $row_labels[]="FX";
                    foreach ($this->m_benches as $bench_id => $bench_data) {
                        $row_labels[]="VALUE";
                        $row_labels[]="SCORE";
                        $row_labels[]="CRITERIA";
                        $row_labels[]="";
                    }
                    $sheet->row($row, $row_labels);
                    $sheet->getStyle("A$row:Z$row")->getFont()->setSize(10);
                    $phpColor = new PHPExcel_Style_Color();
                    $phpColor->setRGB('052968');
                    $sheet->getStyle("A$row:Z$row")->getFont()->setColor($phpColor);
                    $row++;
                    //REQUIREMENTS:
                    foreach($this->m_techcats_scores[$techcat->id]["benches_data"] as $techrequest_id => $request_data) {
                        $row_data = array();
                        $row_data[] = $techrequest_id;
                        $row_data[] = $request_data["title"];
                        $row_data[] = $request_data["feature_id"];
                        foreach($this->m_criticalities as $criticality) {
                            if ($request_data["criticality_id"]==$criticality->id) 
                                $row_data[]="X";
                            else
                                $row_data[]="";
                        }
                        $row_data[] = number_format($this->m_criticalities_total[$request_data["criticality_id"]]->score, 4, ',', '.');
                        $row_data[] = $request_data["input_value"];
                        $row_data[] = $request_data["input_factor"];
                        if (is_numeric($request_data["input_value"]) && is_numeric($request_data["input_factor"]))
                            $row_data[] = $request_data["input_value"]*$request_data["input_factor"];
                        else
                            $row_data[] = $request_data["input_value"];
                        //Criteria:
                        $criteriafunc_id = $request_data["criteriafunc_id"];
                        $row_data[] = $criteriafunc_id;
                        $row_data[] = $criteriafuncs[$criteriafunc_id]->title;
                        //Benches
                        $col_score=13;
                        $requirement_scores = array();
                        foreach($request_data["benches"] as $bench_id => $bench_data) {
                            $requirement_scores[$bench_id]["range"] = $COLS[$col_score].$row;
                            if ($bench_data["criteriafunc_result"]==0) {
                                switch ($request_data["criticality_id"]) {
                                    case 1:
                                        $requirement_scores[$bench_id]["style"] = $style_nogo;
                                        break;
                                    case 2:
                                        $requirement_scores[$bench_id]["style"] = $style_caution;
                                        break;
                                }
                            }
                            $row_data[]=$bench_data["value"];
                            $row_data[]=number_format($bench_data["bench_score"], 5, ',', '.');
                            $row_data[]=$bench_data["criteriafunc_result"];
                            $row_data[]="";
                            $col_score+=4;
                        }
                        $sheet->row($row, $row_data);
                        $range="C$row:Z$row";
                        $sheet->getStyle($range)->applyFromArray($style_center);
                        $sheet->getStyle("A$row:Z$row")->getFont()->setSize(9);
                        //Set requirement background color (each bench)
                        foreach($this->m_benches as $bench_id => $bench_data) {
                            if (isset($requirement_scores[$bench_id]["style"]))
                                $sheet->getStyle($requirement_scores[$bench_id]["range"])->applyFromArray($requirement_scores[$bench_id]["style"]);
                        }
                        $row++;
                    }
                    //RESUME:
                    $row_cat_resume=array();
                    $row_cat_resume[]="";
                    $row_cat_resume[]="CATEGORY RESUME:";
                    $row_cat_resume[]="";
                    //Criticality:
                    foreach($this->m_criticalities as $criticality) {
                        if (isset($this->m_techcats_applicables[$techcat->id]->criticalities_totals[$criticality->id]))
                            $row_cat_resume[] = $this->m_techcats_applicables[$techcat->id]->criticalities_totals[$criticality->id]->total;
                        else
                            $row_cat_resume[] = 0;
                    }
                    //$row_cat_resume[] = number_format($this->m_techcats_scores[$techcat->id]["criticalities_weights_total"], 4, ',', '.');
                    $row_cat_resume[] = $this->m_techcats_applicables[$techcat->id]->score_total;
                    //INPUT TS - FACTOR
                    $row_cat_resume[]="";
                    $row_cat_resume[]="";
                    $row_cat_resume[]="";
                    //CRITERIA:
                    $row_cat_resume[]="";
                    $row_cat_resume[]="";
                    //BENCHES:
                    $col_score=13;
                    $techcat_scores = array();
                    foreach($this->m_techcats_scores[$techcat->id]["benches_score"] as $bench_id => $data) {
                        $techcat_scores[$bench_id]["range"] = $COLS[$col_score].$row;
                        switch($this->m_techcats_scores[$techcat->id]["benches_result"][$bench_id]) {
                            case -1:
                                $label="NO GO";
                                $techcat_scores[$bench_id]["style"] = $style_nogo;
                                break;
                            case 0:
                                $label="CAUTION";
                                $techcat_scores[$bench_id]["style"] = $style_caution;
                                break;
                            case 1:
                                $label="GO";
                                $techcat_scores[$bench_id]["style"] = $style_go;
                                break;
                        }
                        $row_cat_resume[]=$label;
                        $row_cat_resume[]=$this->m_techcats_scores[$techcat->id]["benches_score"][$bench_id];
                        $row_cat_resume[]=number_format($this->m_techcats_scores[$techcat->id]["benches_score"][$bench_id]*100/$this->m_techcats_applicables[$techcat->id]->score_total, 0)."%";
                        $row_cat_resume[]="";
                        $col_score+=4;
                    }
                    $sheet->row($row, $row_cat_resume);
                    $range="A$row:Z$row";
                    $sheet->getStyle($range)->applyFromArray($style_center);
                    $sheet->getStyle($range)->getFont()->setSize(9);
                    //Set background colors:
                    foreach($this->m_benches as $bench_id => $bench_data) {
                        if (isset($techcat_scores[$bench_id]["style"]))
                            $sheet->getStyle($techcat_scores[$bench_id]["range"])->applyFromArray($techcat_scores[$bench_id]["style"]);
                    }
                    $row+=2;
                }
                //FINAL SCORE:
                $row_cat_resume=array();
                $row_cat_resume[]="";
                $row_cat_resume[]="FINAL SCORE:";
                $row_cat_resume[]="";
                //Criticality:
                foreach($this->m_criticalities as $criticality) {
                    if (isset($this->m_criticalities_total[$criticality->id]->total))
                        $row_cat_resume[] = $this->m_criticalities_total[$criticality->id]->total;
                    else
                        $row_cat_resume[] = 0;
                }
                //$row_cat_resume[] = number_format($this->m_techcats_scores[$techcat->id]["criticalities_weights_total"], 4, ',', '.');
                $row_cat_resume[] = "";
                //INPUT TS - FACTOR
                $row_cat_resume[]="";
                $row_cat_resume[]="";
                $row_cat_resume[]="";
                //CRITERIA:
                $row_cat_resume[]="";
                $row_cat_resume[]="";
                //BENCHES:
                $col_score=13;
                $final_scores = array();
                foreach($this->m_benches as $bench_id => $bench_data) {
                    $final_scores[$bench_id]["range"] = $COLS[$col_score].$row;
                    switch($bench_data->result) {
                        case -1:
                            $label="NO GO";
                            $final_scores[$bench_id]["style"] = $style_nogo;
                            break;
                        case 0:
                            $label="CAUTION";
                            $final_scores[$bench_id]["style"] = $style_caution;
                            break;
                        case 1:
                            $label="GO";
                            $final_scores[$bench_id]["style"] = $style_go;
                            break;
                    }
                    $row_cat_resume[]=$label;
                    $row_cat_resume[]=$bench_data->score;
                    $row_cat_resume[]=number_format($bench_data->score*100, 0)."%";
                    $row_cat_resume[]="";
                    $col_score+=4;
                }
                $sheet->row($row, $row_cat_resume);
                $range="A$row:Z$row";
                $sheet->getStyle($range)->applyFromArray($style_center);
                $sheet->getStyle($range)->getFont()->setSize(9);
                //Set background colors (bench final score)
                foreach($this->m_benches as $bench_id => $bench_data) {
                    $sheet->getStyle($final_scores[$bench_id]["range"])->applyFromArray($final_scores[$bench_id]["style"]);
                }
            } catch(Exception $ex) {
                dd($ex);
            }
        });
    }

    private function createTimingSheet($excel) {
        $excel->sheet("C-Timing", function($sheet) {
            $style_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $ratingbenches = $this->m_rating->ratingbenches()->get();

            $sheet->getColumnDimension("A")->setWidth(60);
            $sheet->getColumnDimension("B")->setWidth(500);
            $sheet->getColumnDimension("C")->setWidth(60);
            $row=1;
            $row_data = array();
            $row_data[] = "";
            $row_data[] = "AVAILABILITY";
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = $bench->title;
            }
            $sheet->row($row, $row_data);
            $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
            $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);

            $row++;
            $row_data = array();
            $row_data[] = "";
            $row_data[] = $this->m_timing_availability["request"];
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = number_format($this->m_timing_availability["benches"][$bench->id]["value"],0);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
            }
            $sheet->row($row, $row_data);

            $row++;
            $row_data = array();
            $row_data[] = "";
            $row_data[] = "AVAILABILITY SCORE";
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = number_format($this->m_timing_availability["benches"][$bench->id]["percent"],0);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
            }
            $sheet->row($row, $row_data);


            $row+=2;
            $sheet->row($row, ["","TEST EXECUTION TIME"]);
            $sheet->getStyle("B$row")->getFont()->setBold(true);
            for($SUBCAT_ID=1;$SUBCAT_ID<=count($this->m_timing_executions);$SUBCAT_ID++) {
                $row++;
                $row_data = array();
                $row_data[] = $SUBCAT_ID;
                $row_data[] = $this->m_timing_executions[$SUBCAT_ID]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = $bench->title;
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
                $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);
                $row++;
                foreach($this->m_timing_executions[$SUBCAT_ID]["requests"] as $item => $request) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $request["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = number_format($request["values"][$bench->id],2);
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
                    $row++;
                }
                //Subtotals:
                $row_data = array();
                $row_data[] = "";
                $row_data[] = $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = number_format($this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"][$bench->id],2);
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:E$row")->getFont()->setSize(9);
                $row++;
            }
            $row++;

            //FLEXIBILITY:
            $row_data = array();
            $row_data[] = "";
            $row_data[] = "FLEXIBILITY";
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = $bench->title;
            }
            $sheet->row($row, $row_data);
            $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
            $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);

            $row++;
            $row_data = array();
            $row_data[] = "";
            $row_data[] = "FLEXIBILITY SCORE";
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = number_format($this->m_timing_flexibility["benches"][$bench->id]["percent"],0);
            }
            $sheet->row($row, $row_data); 
            $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
            $row+=2;

            //FINAL SCORE:
            $row_data = array();
            $row_data[] = "";
            $row_data[] = "FINAL SCORE";
            foreach($ratingbenches as $ratingbench) {
                $bench = $ratingbench->bench()->first();
                $row_data[] = $bench->title;
            }
            $sheet->row($row, $row_data);
            $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
            $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);
            $row++;
            foreach ($this->m_timing_finalscore as $final_score) {
                $row_data = array();
                $row_data[] = "";
                $row_data[] = $final_score["request"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = number_format($final_score["benches"][$bench->id]["percent"],2);
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
                $row++;
            }
        });
    }

    private function createEconomicsSheet($excel) {
        $excel->sheet("D-Economics", function($sheet) {
            $style_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $ratingbenches = $this->m_rating->ratingbenches()->get();

            $sheet->getColumnDimension("A")->setWidth(60);
            $sheet->getColumnDimension("B")->setWidth(500);
            $sheet->getColumnDimension("C")->setWidth(60);
            $row=1;
            //BUSINESS CASE:
            $sheet->row($row, ["","BUSINESS CASE"]);
            $sheet->getStyle("B$row")->getFont()->setBold(true);
            for($SUBCAT_ID=1;$SUBCAT_ID<=count($this->m_economic_business);$SUBCAT_ID++) {
                $row++;
                $ratingbenches = $this->m_rating->ratingbenches()->get();
                $row_data = array();
                $row_data[] = $SUBCAT_ID;
                $row_data[] = $this->m_economic_business[$SUBCAT_ID]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = $bench->title;
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
                $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);
                $row++;
                foreach($this->m_economic_business[$SUBCAT_ID]["requests"] as $item => $request) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $request["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = $request["values"][$bench->id];
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:E$row")->getFont()->setSize(9);
                    $row++;
                }
                //Subtotals:
                $row_data = array();
                $row_data[] = "";
                $row_data[] = $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"][$bench->id];
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
                $row++;
            }
            $row++;

            //ALTERNATIVE CASE:
            $sheet->row($row, ["","ALTERNATIVE CASE"]);
            $sheet->getStyle("B$row")->getFont()->setBold(true);
            for($SUBCAT_ID=1;$SUBCAT_ID<=count($this->m_economic_alternative);$SUBCAT_ID++) {
                $row++;
                $ratingbenches = $this->m_rating->ratingbenches()->get();
                $row_data = array();
                $row_data[] = $SUBCAT_ID;
                $row_data[] = $this->m_economic_alternative[$SUBCAT_ID]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = $bench->title;
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
                $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);
                $row++;
                foreach($this->m_economic_alternative[$SUBCAT_ID]["requests"] as $item => $request) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $request["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = $request["values"][$bench->id];
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:E$row")->getFont()->setSize(9);
                    $row++;
                }
                //Subtotals:
                if (isset($this->m_economic_alternative[$SUBCAT_ID]["subtotals"])) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["weights"][$bench->id];
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
                    $row++;
                }
                //Score:
                if (isset($this->m_economic_alternative[$SUBCAT_ID]["score"])) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $this->m_economic_alternative[$SUBCAT_ID]["score"]["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = $this->m_economic_alternative[$SUBCAT_ID]["score"]["benches"][$bench->id];
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:G$row")->getFont()->setSize(9);
                    $row++;
                }
            }
            $row++;

            //FINAL SCORE:
            $sheet->row($row, ["","FINAL SCORE"]);
            $sheet->getStyle("B$row")->getFont()->setBold(true);
            for($SUBCAT_ID=1;$SUBCAT_ID<=count($this->m_economic_finalscore);$SUBCAT_ID++) {
                $row++;
                $ratingbenches = $this->m_rating->ratingbenches()->get();
                $row_data = array();
                $row_data[] = "";
                $row_data[] = $this->m_economic_finalscore[$SUBCAT_ID]["title"];
                foreach($ratingbenches as $ratingbench) {
                    $bench = $ratingbench->bench()->first();
                    $row_data[] = $bench->title;
                }
                $sheet->row($row, $row_data);
                $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("A$row:G$row")->getFont()->setSize(10);
                $sheet->getStyle("C$row:G$row")->applyFromArray($style_center);
                $row++;
                foreach($this->m_economic_finalscore[$SUBCAT_ID]["requests"] as $item => $request) {
                    $row_data = array();
                    $row_data[] = "";
                    $row_data[] = $request["title"];
                    foreach($ratingbenches as $ratingbench) {
                        $bench = $ratingbench->bench()->first();
                        $row_data[] = $request["values"][$bench->id];
                    }
                    $sheet->row($row, $row_data);
                    $sheet->getStyle("A$row:E$row")->getFont()->setSize(9);
                    $row++;
                }
            }
        });
    }

    private function createFinalSheet($excel) {
        $excel->sheet("E-Final Assessment", function($sheet) {
            
        });
    }

    //Show on screen technical rating score:
    public function technical($rating_id,$techcat_id=0) {
        $this->m_rating = Rating::find($rating_id);
        $this->technicalRating();
        //dd($this->m_techcats_applicables);
        return view('ratingtools.scores.technical')
            ->with('rating',$this->m_rating)
            ->with('benches',$this->m_benches)
            ->with('techcat_id',$techcat_id)
            ->with('techcats',$this->m_techcats_applicables)
            ->with('criticalities',$this->m_criticalities)
            ->with('criteriafuncs',$this->m_criteriafuncs)
            ->with('cat_scores',$this->m_techcats_scores)
            ->with('criticalities_totals',$this->m_criticalities_total);
    }

    //Internal calculates for a technical rating (to show on screen, export excel, or store on server)
    private function technicalRating() {
        //Benches:
        $this->ratingBenches($this->m_rating);
        //dd($this->m_benches);

        $this->m_techcats_scores = array();

        //Criticalities:
        $this->m_criticalities = Criticality::where('type','=','1')->get()->keyby('id');
        $this->m_criteriafuncs = Criteriafunc::all()->keyby('id');
        //Input values:
        $this->m_ratinginputrequests = $this->m_rating->ratinginputrequests()->get()->keyBy('inputrequest_id');
        //dd($this->m_ratinginputrequests);
        
        //Techcats:
        $this->m_techcats = $this->m_rating->techsheet()->first()->techcats()->orderby('id')->get()->keyBy('id');
        //Applicables techcats in the rating:
        $this->getTechcatsApplicables($this->m_rating->id);
        
        //Criticalities totals for each applicable techcat:
        $techcats = array();
        foreach ($this->m_techcats_applicables as $id => $data) {
            $techcats[] = $id;
            $this->m_techcats_applicables[$id]->criticalities_totals = $this->criticalitiesTechcatTotals($this->m_rating->id,$this->m_rating->techsheet_id,$id);
        }
        //dd($this->m_techcats_applicables);

        //Calculate criticalities scores:
        $this->criticalitiesTotalScores($this->m_rating->id,$this->m_rating->techsheet_id,$techcats);
        //dd($this->m_criticalities_total);
        /*
        //Check point: Sum of crit total * crit score must be 1:
        $total=0;
        foreach ($this->m_criticalities_total as $crit_id => $data) {
            $total += $data->total * $data->score; 
        }
        dd($total);
        */

        //Update criticality score for each category:
        foreach ($this->m_techcats_applicables as $id => $data) {
            foreach ($this->m_criticalities as $criticality_id => $criticality) {
                //First check if exist this criticality_id (maybe some criticality (P/S/T) don't have any requirement)
                if (isset($this->m_techcats_applicables[$id]->criticalities_totals[$criticality_id])) {
                    $this->m_techcats_applicables[$id]->criticalities_totals[$criticality_id]->score = $this->m_techcats_applicables[$id]->criticalities_totals[$criticality_id]->total*$this->m_criticalities_total[$criticality_id]->score;
                    $data->score_total+=$this->m_techcats_applicables[$id]->criticalities_totals[$criticality_id]->score;
                }
            }
        }
        //dd($this->m_techcats_applicables);

        //Requests of each techcat:
        $cat_scores = array();
        //Only applicable techcats:
        foreach ($this->m_techcats_applicables as $id => $data) {
            //Get techcat object:
            $item_cat = $this->m_techcats[$id];
            $scores = array();
            $benches_cat_score = array();
            $benches_cat_result = array();
            foreach ($item_cat->techrequests()->orderBy('ordering','asc')->get() as $techrequest) {
                //Check criticality level:
                $criticality_id = $techrequest->ratingtechrequests()->where('rating_id','=',$this->m_rating->id)->first()->criticality_id;
                //Check if depends on any inputrequest, and get value:
                $inputrequest = $techrequest->inputrequest()->first();
                $input_value=null;  //For validate on criteriafunc
                $input_value_to_show=null;//To show
                if (isset($inputrequest)) {
                    $input_value = $this->m_ratinginputrequests[$inputrequest->id]->value;
                    $input_value_to_show = $input_value;
                }

                //Check if techrequest is relating to a feature (If not, it is an issue):
                if (!isset($techrequest->feature_id)) 
                    Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title: No feature relation!");

                //Benches scores for this techrequest:
                $benches=array();
                foreach ($this->m_benches as $bench_id => $bench_data) {
                    if ($this->log_dev) 
                        Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title");

                    //By default, this requirement in this bench score is GO
                    if (!isset($benches_cat_result[$bench_id])) 
                        $benches_cat_result[$bench_id] = self::RES_GO;

                    //Warning!! in develop phase, techrequest->feature_id can be null, but in production it would be an issue (not possible):
                    $bench_value=null;
                    $bench_value_to_show=null;
                    $criteria_score = 0;
                    if (isset($techrequest->feature_id)) {
                        //Check if exist bench_id/feature_id on bench_feature table:
                        $bench_feature = Bench::find($bench_id)->features()->where('feature_id','=',$techrequest->feature_id)->first();
                        if (isset($bench_feature)) {
                            $bench_value = $bench_feature->pivot->value;
                            $bench_value_to_show = $bench_value;
                            //Check response type of the feature:
                            switch ($techrequest->feature()->first()->responsetype_id) {
                                case '1': //TEXT
                                    Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) Feature responsetype Text!! Bench value: $bench_value");
                                        $bench_value=null;
                                    break;
                                case '2': // Yes / No
                                    if ($bench_value!="Yes" && $bench_value!="No") {
                                        Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) Feature responsetype Yes/No and bench value incorrect: $bench_value");
                                        $bench_value=null;
                                    }
                                    break;
                                case '3': //Numeric
                                    if (isset($bench_value) && !is_numeric($bench_value)) {
                                        Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) Feature responsetype Numeric and bench value incorrect: $bench_value");
                                        $bench_value=null;
                                    }
                                    break;
                                case '4':   //Date
                                    $d = DateTime::createFromFormat("Y-m-d", $bench_value);
                                    if ($d && $d->format("Y-m-d") === $bench_value) {
                                        //Valid date, convert to timestamp to compare:
                                        $bench_value = strtotime($bench_value);
                                        $input_value = strtotime($input_value_to_show);
                                    } else {
                                        Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) Feature responsetype Date and bench value incorrect: $bench_value");
                                        $bench_value=null;
                                    }
                                    break;
                                case '5':   //Item/number
                                    $items = DB::table('bench_feature_brands')
                                        ->select(DB::raw('count(*) as total'))
                                        ->where('bench_id','=',$bench_id)
                                        ->where('feature_id','=',$techrequest->feature_id)
                                        ->count();
                                    if ($items>0) $bench_value="Yes"; else $bench_value="No";
                                    break;
                                default:
                                    Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) Responsetype invalid!!");
                                    $bench_value=null;
                                    break;
                            }
                        } else {
                            //Not exist relation between bench_id and feature_id, maybe incorrect specific techsheet, so this bench is not adecuate for tis rating:
                            Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title -Feature_id:$techrequest->feature_id Not exist relation between bench_id and feature_id on table bench_feature, so this bench is not adecuate for tis rating");
                        }
                        //Check criteria score for current bench:
                        if (isset($bench_value))
                            $criteria_score = $this->getCriteriafuncResult($techrequest,$bench_data->title,$bench_value,$input_value);
                            //echo "<p>criteria_score = getCriteriafuncResult(techrequest,$bench_data->title,bench_value,$input_value);</p>";
                        else {
                            $criteria_score=0;
                            $bench_value_to_show=null;
                        }
                    }
                    $benches[$bench_id]["value"] = $bench_value_to_show;

                    //If score <0 error => Log!
                    if ($criteria_score<0) {
                        Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) -score: $criteria_score");
                        $criteria_score=0;
                    } 
                    //Define criteria score (GO, NOGO or CAUTION)
                    $benches[$bench_id]["criteriafunc_result"] = $criteria_score;
                    if ($criteria_score==0) {
                        switch ($criticality_id) {
                            case 1:
                                $benches_cat_result[$bench_id] = self::RES_NOGO;
                                $bench_data->result = self::RES_NOGO;
                                break;
                            case 2:
                                if ($benches_cat_result[$bench_id]!=self::RES_NOGO) {
                                    $benches_cat_result[$bench_id] = self::RES_CAUTION;
                                    if (!isset($bench_data->result))
                                        $bench_data->result = self::RES_CAUTION;
                                }
                                break;
                            case 3:
                                //Do not affect, always GO (green) only do not add the score
                                break;
                            default:
                                //If score <0 error => Log!
                                Log::channel('custom')->info("TECHREQ: $techrequest->id: $techrequest->title (Bench($bench_id): $bench_data->title) -score: $criteria_score -ERR: criticality_id not in range (1,2,3) -> $criticality_id");
                                break;
                        }
                    }
                    //Calculate final score (depending on criticality P/S/T and it weight)
                    switch ($criteria_score) {
                        case 1:
                            //if binary add full score, if scale, add half score:
                            if ($techrequest->criteriafunc()->first()->criteria_id==1)
                                $benches[$bench_id]["bench_score"] = $this->m_criticalities_total[$criticality_id]->score;
                            else
                                $benches[$bench_id]["bench_score"] = $this->m_criticalities_total[$criticality_id]->score/2;
                            break;
                        case 2:
                            $benches[$bench_id]["bench_score"] = $this->m_criticalities_total[$criticality_id]->score;
                            break;
                        default:
                            $benches[$bench_id]["bench_score"] = 0;
                            break;
                    }
                    //Acumulate cat score total:
                    if (isset($benches_cat_score[$bench_id])) 
                        $benches_cat_score[$bench_id]+=$benches[$bench_id]["bench_score"];
                    else
                        $benches_cat_score[$bench_id]=$benches[$bench_id]["bench_score"];
                    //Acumulate bench score total:
                    $bench_data->score += $benches[$bench_id]["bench_score"];
                }
                $scores[$techrequest->id]["title"] = $techrequest->title;
                if (isset($techrequest->feature_id)) {
                    $scores[$techrequest->id]["feature_id"] = $techrequest->feature_id;
                    $scores[$techrequest->id]["value"] = $techrequest->value;
                    $scores[$techrequest->id]["range_x"] = $techrequest->range_x;
                    $scores[$techrequest->id]["range_y"] = $techrequest->range_y;
                } else {
                    $scores[$techrequest->id]["feature_id"] = -1;
                    $scores[$techrequest->id]["value"] = null;
                    $scores[$techrequest->id]["range_x"] = null;
                    $scores[$techrequest->id]["range_y"] = null;
                }

                //Criticality, criteriafunc and inputdata value for this techrequest:
                $scores[$techrequest->id]["criticality_id"] = $criticality_id;
                $scores[$techrequest->id]["criteriafunc_id"] = $techrequest->criteriafunc_id;
                $scores[$techrequest->id]["input_value"] = $input_value_to_show;
                //If the input data relating to this tech request has an input factor, must be shown:
                $scores[$techrequest->id]["input_factor"] = $techrequest->inputfactor;

                //Check if input_value required (depends of criteriafunc), and is null:
                //if ($techrequest->criteriafunc()->first()->askinput>0 && strlen($input_value_to_show)<=0) {
                if ($techrequest->criteriafunc()->first()->askinput>0 && strlen($input_value_to_show)<=0) {
                    $scores[$techrequest->id]["input_value_fail"] = true;
                } else {
                    $scores[$techrequest->id]["input_value_fail"] = false;
                }
                
                //Benches data for this techrequest:
                $scores[$techrequest->id]["benches"] = $benches;
            }
            $this->m_techcats_scores[$item_cat->id]["benches_data"] = $scores;
            $this->m_techcats_scores[$item_cat->id]["benches_score"] = $benches_cat_score;
            $this->m_techcats_scores[$item_cat->id]["benches_result"] = $benches_cat_result;
        }
        //dd($this->m_benches);
        //dd($this->m_techcats_scores);

    }

    //Show on screen timing rating score:
    public function timing($rating_id) {
        $this->m_rating = Rating::find($rating_id);
        $this->timingRating();
        return view('ratingtools.scores.timing')
            ->with('rating',$this->m_rating)
            ->with('cats',$this->m_timing_cats)
            ->with('benches',$this->m_benches)
            ->with('availability',$this->m_timing_availability)
            ->with('executions',$this->m_timing_executions)
            ->with('flexibility',$this->m_timing_flexibility)
            ->with('finalscore',$this->m_timing_finalscore);
    }

    //Internal calculates for a timing rating (to show on screen, export excel, or store on server)
    private function timingRating() {
        //Benches:
        $this->m_benches = array();
        $this->m_timing_availability = array();
        $this->m_timing_executions = array();
        $this->m_timing_flexibility = array();
        $this->m_timing_finalscore = array();

        //Load categories:
        $this->m_timing_cats = $this->m_rating->timesheet()->first()->timecats()->get();
        //dd($this->m_timing_cats);

        //Benches of the rating:
        $ratingbenches = $this->m_rating->ratingbenches()->get()->keyBy('id');

        //Benches subtotals acumulate:
        $benches_totals = array();
        foreach ($ratingbenches as $id => $ratingbench) {
            $this->m_benches[$ratingbench->bench_id] = $ratingbench->bench()->first()->title;
            $benches_totals[$ratingbench->bench_id] = 0;
        }

        
        //AVAILABILITY
        //This is a special timecat with only 1 subcat (General) and only 1 timerequest (Number of weeks ...)
        $item_cat = $this->m_rating->timesheet()->first()->timecats()->where('type','=',1)->first();
        $timerequest = $item_cat->timesubcats()->first()->timerequests()->first();
        $this->m_timing_availability["request"] = $timerequest->title;
        $timerequestsetts = $timerequest->timerequestsetts()->orderBy('value','Asc')->get();
        //dd($timerequestsetts);
        foreach ($ratingbenches as $ratingbench) {
            $ratingtimerequest = Ratingtimerequest::where('timerequest_id','=',$timerequest->id)
                ->where('ratingbench_id','=',$ratingbench->id)->first();
            if (isset($ratingtimerequest->value))
                foreach ($timerequestsetts as $id => $timerequestsett) {
                    $percent_value = $timerequestsett->percent;
                    if ($timerequestsett->value>=$ratingtimerequest->value) break;
                }
            else
                $percent_value = 0;
            $this->m_timing_availability["benches"][$ratingbench->bench_id]["value"]=$ratingtimerequest->value;
            $this->m_timing_availability["benches"][$ratingbench->bench_id]["percent"]=$percent_value;
        }
        //dd($this->m_timing_availability);

        //EXECUTION
        //Laboratory subcat:
        $SUBCAT_ID=1;
        $item_cat = $this->m_rating->timesheet()->first()->timecats()->where('type','=',2)->first();
        $item_subcat = $item_cat->timesubcats()->where('title','like',"%Laboratory%")->first();
        $this->m_timing_executions[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->timerequests()->orderBy('ordering','asc')->get() as $item_request) {
            $values = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingtimerequests = $ratingbench->ratingtimerequests()->get()->keyBy('timerequest_id');
                $values[$ratingbench->bench_id] = $ratingtimerequests[$item_request->id]->value;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingtimerequests[$item_request->id]->value;
                //Acumulate totals:
                $benches_totals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;

            }
            $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "unit" => "w"
            ];
        }
        //Subtotals:
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL LABORATORY";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["unit"] = "w";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];

        //Incidents subcat:
        $SUBCAT_ID++;
        $item_subcat = $item_cat->timesubcats()->where('title','like',"%Incidents%")->first();
        $this->m_timing_executions[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->timerequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingtimerequests = $ratingbench->ratingtimerequests()->get()->keyBy('timerequest_id');
                $values[$ratingbench->bench_id] = $ratingtimerequests[$item_request->id]->value;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingtimerequests[$item_request->id]->value;
                //Acumulate totals:
                $benches_totals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;

            }
            $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "unit" => "w"
            ];
        }
        //Subtotals:
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL INCIDENTS";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["unit"] = "w";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];

        //Totals LABORATORY + INCIDENTS:
        $SUBCAT_ID++;
        $this->m_timing_executions[$SUBCAT_ID]["title"] = "TOTAL WEEKS (LABORATORY + INCIDENTS)";
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "TOTAL",
            "values" => $benches_totals,
            "unit" => "w"
        ];
        //Minor and differences:
        $minor = null;
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($minor) || $benches_totals[$ratingbench->bench_id]<$minor) $minor=$benches_totals[$ratingbench->bench_id];
        }

        $differences = array();
        foreach ($ratingbenches as $ratingbench) {
            $differences[$ratingbench->bench_id] = $benches_totals[$ratingbench->bench_id]-$minor;
        }
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference in weeks vs faster lab",
            "values" => $differences,
            "unit" => "w"
        ];
        $percents = array();
        foreach ($ratingbenches as $ratingbench) {
            if($minor>0)
                $percents[$ratingbench->bench_id] = ($differences[$ratingbench->bench_id]/$minor)*100;
            else
                $percents[$ratingbench->bench_id] = 100;
        }
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference % vs faster lab",
            "values" => $percents,
            "unit" => "%"
        ];
        $ratios = array();
        foreach ($ratingbenches as $ratingbench) {
            if($minor>0)
                $ratios[$ratingbench->bench_id] = ($minor/$benches_totals[$ratingbench->bench_id])*100;
            else
                $ratios[$ratingbench->bench_id] = 0;
        }
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"] = "LABORATORY + INCIDENTS SCORE";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"] = $ratios;
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($ratios, max($ratios))[0];

        //Transportation subcat:
        $SUBCAT_ID++;
        $item_subcat = $item_cat->timesubcats()->where('title','like',"%Transportation%")->first();
        $this->m_timing_executions[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->timerequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingtimerequests = $ratingbench->ratingtimerequests()->get()->keyBy('timerequest_id');
                $values[$ratingbench->bench_id] = $ratingtimerequests[$item_request->id]->value;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingtimerequests[$item_request->id]->value;
                //Acumulate totals:
                $benches_totals[$ratingbench->bench_id]+=$ratingtimerequests[$item_request->id]->value;

            }
            $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "unit" => "w"
            ];
        }
        //Subtotals:
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL TRANSPORTATION";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["unit"] = "w";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];

        //Totals LABORATORY + INCIDENTS + TRANSPORTATION:
        $SUBCAT_ID++;
        $this->m_timing_executions[$SUBCAT_ID]["title"] = "TOTAL WEEKS (LABORATORY + INCIDENTS + TRNSP)";
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "TOTAL",
            "values" => $benches_totals,
            "unit" => "w"
        ];
        //Minor and differences:
        $minor = null;
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($minor) || $benches_totals[$ratingbench->bench_id]<$minor) $minor=$benches_totals[$ratingbench->bench_id];
        }
        $differences = array();
        foreach ($ratingbenches as $ratingbench) {
            $differences[$ratingbench->bench_id] = $benches_totals[$ratingbench->bench_id]-$minor;
        }
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference in weeks vs faster lab",
            "values" => $differences,
            "unit" => "w"
        ];
        $percents = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($minor>0)
                $percents[$ratingbench->bench_id] = ($differences[$ratingbench->bench_id]/$minor)*100;
            else
                $percents[$ratingbench->bench_id] = 100;
        }
        $this->m_timing_executions[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference % vs faster lab",
            "values" => $percents,
            "unit" => "%"
        ];
        $ratios = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($minor>0)
                $ratios[$ratingbench->bench_id] = ($minor/$benches_totals[$ratingbench->bench_id])*100;
            else
                $ratios[$ratingbench->bench_id] = 0;
        }
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["title"] = "LABORATORY + INCIDENTS + TRANSP SCORE";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["values"] = $ratios;
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_timing_executions[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($ratios, max($ratios))[0];
        //dd($this->m_timing_executions);

        //FLEXIBILITY
        //This is a special timecat with only 1 subcat (General) and only 1 timerequest (Flexibility percentage)
        $item_cat = $this->m_rating->timesheet()->first()->timecats()->where('type','=',3)->first();
        $timerequest = $item_cat->timesubcats()->first()->timerequests()->first();
        $this->m_timing_flexibility["request"] = $timerequest->title;
        $timerequestsetts = $timerequest->timerequestsetts()->orderBy('value','Asc')->get();
        //dd($timerequestsetts);
        foreach ($ratingbenches as $ratingbench) {
            $ratingtimerequest = Ratingtimerequest::where('timerequest_id','=',$timerequest->id)
                ->where('ratingbench_id','=',$ratingbench->id)->first();
            foreach ($timerequestsetts as $id => $timerequestsett) {
                $percent_value = $timerequestsett->percent;
                if ($timerequestsett->value>=$ratingtimerequest->value) break;
            }
            $this->m_timing_flexibility["benches"][$ratingbench->bench_id]["value"]=$ratingtimerequest->value;
            $this->m_timing_flexibility["benches"][$ratingbench->bench_id]["percent"]=$percent_value;
        }
        //dd($this->m_timing_flexibility);


        //SCORE
        $this->m_timing_finalscore[1]["request"] = "FINAL TIME RELATED SCORE. Laboratory related times";
        foreach ($ratingbenches as $ratingbench) {
            $final_score=0;
            foreach ($this->m_timing_cats as $timecat) {
                $score_weight = $timecat->score_weight/100;
                switch ($timecat->type) {
                    case 1: //Availability
                        $final_score += $score_weight*$this->m_timing_availability["benches"][$ratingbench->bench_id]["percent"];
                        break;
                    case 2: //Execution
                        $final_score += $score_weight*$this->m_timing_executions[1]["subtotals"]["values"][$ratingbench->bench_id];
                        break;
                    case 3: //Flexibility
                        $final_score += $score_weight*$this->m_timing_flexibility["benches"][$ratingbench->bench_id]["percent"];
                        break;
                }
                
            }
            $this->m_timing_finalscore[1]["benches"][$ratingbench->bench_id]["percent"]=$final_score;
        }

        $this->m_timing_finalscore[2]["request"] = "FINAL TIME RELATED SCORE. Laboratory + incidents times";
        foreach ($ratingbenches as $ratingbench) {
            $final_score=0;
            foreach ($this->m_timing_cats as $timecat) {
                $score_weight = $timecat->score_weight/100;
                switch ($timecat->type) {
                    case 1: //Availability
                        $final_score += $score_weight*$this->m_timing_availability["benches"][$ratingbench->bench_id]["percent"];
                        break;
                    case 2: //Execution
                        $final_score += $score_weight*$this->m_timing_executions[3]["subtotals"]["values"][$ratingbench->bench_id];
                        break;
                    case 3: //Flexibility
                        $final_score += $score_weight*$this->m_timing_flexibility["benches"][$ratingbench->bench_id]["percent"];
                        break;
                }
                
            }
            $this->m_timing_finalscore[2]["benches"][$ratingbench->bench_id]["percent"]=$final_score;
        }

        $this->m_timing_finalscore[3]["request"] = "FINAL TIME RELATED SCORE. Laboratory + Transportation times";
        foreach ($ratingbenches as $ratingbench) {
            $final_score=0;
            foreach ($this->m_timing_cats as $timecat) {
                $score_weight = $timecat->score_weight/100;
                switch ($timecat->type) {
                    case 1: //Availability
                        $final_score += $score_weight*$this->m_timing_availability["benches"][$ratingbench->bench_id]["percent"];
                        break;
                    case 2: //Execution
                        $final_score += $score_weight*$this->m_timing_executions[5]["subtotals"]["values"][$ratingbench->bench_id];
                        break;
                    case 3: //Flexibility
                        $final_score += $score_weight*$this->m_timing_flexibility["benches"][$ratingbench->bench_id]["percent"];
                        break;
                }
                
            }
            $this->m_timing_finalscore[3]["benches"][$ratingbench->bench_id]["percent"]=$final_score;
        }
        //dd($this->m_timing_finalscore);

        //Add a final score timecat:
        $timecat = new Timecat();
        $timecat->title = "Final Score";
        $timecat->type = 4;
        $this->m_timing_cats[] = $timecat;
        //dd($this->m_timing_cats);
        
    }

    //Show on screen economics rating score:
    public function economics($rating_id) {
        $this->m_rating = Rating::find($rating_id);
        $this->economicsRating();
        //dd($this->m_economic_business);
        $statecolors = ['','white','khaki','Palegreen'];
        return view('ratingtools.scores.economic')
                ->with('rating',$this->m_rating)
                ->with('cats',$this->m_economic_cats)
                ->with('benches',$this->m_benches)
                ->with('statecolors',$statecolors)
                ->with('business',$this->m_economic_business)
                ->with('alternative',$this->m_economic_alternative)
                ->with('finalscore',$this->m_economic_finalscore);
    }

    //Internal calculates for a economics rating (to show on screen, export excel, or store on server)
    private function economicsRating() {
        //Benches:
        $this->m_benches = array();
        $this->m_economic_business = array();
        $this->m_economic_alternative = array();
        $this->m_economic_finalscore = array();

        //Load categories:
        $this->m_economic_cats = $this->m_rating->economicsheet()->first()->economiccats()->get();
        //dd($this->m_timing_cats);

        //Benches of the rating:
        $ratingbenches = $this->m_rating->ratingbenches()->get()->keyBy('id');

        //Benches subtotals acumulate:
        $benches_totals = array();
        foreach ($ratingbenches as $id => $ratingbench) {
            $this->m_benches[$ratingbench->bench_id] = $ratingbench->bench()->first()->title;
            $benches_totals[$ratingbench->bench_id] = 0;
        }

        //BUSINESS CASE:
        $SUBCAT_ID=1;
        $item_cat = $this->m_rating->economicsheet()->first()->economiccats()->where('type','=',1)->first();
        //CAPEX subcat:
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Capex%")->first();
        $this->m_economic_business[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->economicrequests()->orderBy('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingeconomicrequests[$item_request->id]->value;
                //Acumulate totals:
                $benches_totals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;

            }
            $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "unit" => "€"
            ];
        }
        //dd(array_keys($bench_subtotals, min($bench_subtotals))); 
        //Subtotals:
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL CAPEX";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "€";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = true;

        //OPEX subcat:
        $SUBCAT_ID++;
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Opex%")->first();
        $this->m_economic_business[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->economicrequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingeconomicrequests[$item_request->id]->value;
                //Acumulate totals:
                $benches_totals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;
            }
            $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "unit" => "€"
            ];
        }
        //Subtotals:
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL OPEX";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "€";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = true;

        //Totals CAPEX + OPEX:
        $SUBCAT_ID++;
        $this->m_economic_business[$SUBCAT_ID]["title"] = "TOTAL PRICE OF TEST (CAPEX + OPEX)";
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "TOTAL",
            "values" => $benches_totals,
            "unit" => "€"
        ];
        //Minor and differences:
        $minor = null;
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($minor) || $benches_totals[$ratingbench->bench_id]<$minor) $minor=$benches_totals[$ratingbench->bench_id];
        }
        $differences = array();
        foreach ($ratingbenches as $ratingbench) {
            $differences[$ratingbench->bench_id] = $benches_totals[$ratingbench->bench_id]-$minor;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference € vs cheapest",
            "values" => $differences,
            "unit" => "€"
        ];
        $percents = array();
        foreach ($ratingbenches as $ratingbench) {
            if($minor>0)
                $percents[$ratingbench->bench_id] = ($differences[$ratingbench->bench_id]/$minor)*100;
            else
                $percents[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference % vs cheapest",
            "values" => $percents,
            "unit" => "%"
        ];
        $ratios = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($benches_totals[$ratingbench->bench_id]>0)
                $ratios[$ratingbench->bench_id] = ($minor/$benches_totals[$ratingbench->bench_id])*100;
            else
                $ratios[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL PRICE SCORE";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $ratios;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($ratios, max($ratios))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = false;


        //TRANSPORTATION subcat:
        $SUBCAT_ID++;
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Transportation%")->first();
        $this->m_economic_business[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->economicrequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingeconomicrequests[$item_request->id]->value;
                $benches_totals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;

            }
            $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "unit" => "€"
            ];
        }
        //Subtotals:
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL TRANSPORTATION";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "€";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = true;

        //TOTAL COST OF TEST = TOTAL PRICE + TRANSPORT + OTHERS
        $SUBCAT_ID++;
        $this->m_economic_business[$SUBCAT_ID]["title"] = "TOTAL COST OF TEST (TOTAL PRICE + TRANSPORT + OTHERS)";
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "TOTAL",
            "values" => $benches_totals,
            "unit" => "€"
        ];
        //Minor and differences:
        $minor = null;
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($minor) || $benches_totals[$ratingbench->bench_id]<$minor) $minor=$benches_totals[$ratingbench->bench_id];
        }
        $differences = array();
        foreach ($ratingbenches as $ratingbench) {
            $differences[$ratingbench->bench_id] = $benches_totals[$ratingbench->bench_id]-$minor;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference € vs cheapest",
            "values" => $differences,
            "unit" => "€"
        ];
        $percents = array();
        foreach ($ratingbenches as $ratingbench) {
            if($minor>0)
                $percents[$ratingbench->bench_id] = ($differences[$ratingbench->bench_id]/$minor)*100;
            else
                $percents[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference % vs cheapest",
            "values" => $percents,
            "unit" => "%"
        ];
        $ratios = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($benches_totals[$ratingbench->bench_id]>0)
                $ratios[$ratingbench->bench_id] = ($minor/$benches_totals[$ratingbench->bench_id])*100;
            else
                $ratios[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL COST SCORE";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $ratios;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($ratios, max($ratios))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = false;

        //OPPORTUNITY subcat:
        $SUBCAT_ID++;
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Opportunity%")->first();
        $this->m_economic_business[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        foreach ($item_subcat->economicrequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (isset($bench_subtotals[$ratingbench->bench_id]))
                    $bench_subtotals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;
                else
                    $bench_subtotals[$ratingbench->bench_id]=$ratingeconomicrequests[$item_request->id]->value;
                $benches_totals[$ratingbench->bench_id]+=$ratingeconomicrequests[$item_request->id]->value;

            }
            $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "unit" => "€"
            ];
        }
        //Subtotals:
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "TOTAL OPPORTUNITY";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $bench_subtotals;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "€";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($bench_subtotals, min($bench_subtotals))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = true;

        //REAL COST OF TEST = TOTAL COST + OPPORTUNITY COST
        $SUBCAT_ID++;
        $this->m_economic_business[$SUBCAT_ID]["title"] = "REAL COST OF TEST (TOTAL COST + OPPORTUNITY COST)";
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "TOTAL",
            "values" => $benches_totals,
            "unit" => "€"
        ];
        //Minor and differences:
        $minor = null;
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($minor) || $benches_totals[$ratingbench->bench_id]<$minor) $minor=$benches_totals[$ratingbench->bench_id];
        }
        $differences = array();
        foreach ($ratingbenches as $ratingbench) {
            $differences[$ratingbench->bench_id] = $benches_totals[$ratingbench->bench_id]-$minor;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference € vs cheapest",
            "values" => $differences,
            "unit" => "€"
        ];
        $percents = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($minor>0)
                $percents[$ratingbench->bench_id] = ($differences[$ratingbench->bench_id]/$minor)*100;
            else
                $percents[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["requests"][] = [
            "title" => "Difference % vs cheapest",
            "values" => $percents,
            "unit" => "%"
        ];
        $business_ratios = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($benches_totals[$ratingbench->bench_id]>0)
                $business_ratios[$ratingbench->bench_id] = ($minor/$benches_totals[$ratingbench->bench_id])*100;
            else
                $business_ratios[$ratingbench->bench_id] = 0;
        }
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["title"] = "REAL COST SCORE";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["values"] = $business_ratios;
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["best_bench"] = array_keys($business_ratios, max($business_ratios))[0];
        $this->m_economic_business[$SUBCAT_ID]["subtotals"]["visibility"] = false;
        //dd($this->m_economic_business);


        //ALTERNATIVE CASE:
        $item_cat = $this->m_rating->economicsheet()->first()->economiccats()->where('type','=',2)->first();
        $SUBCAT_ID=1;
        //DELAY subcat:
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Delays%")->first();
        $this->m_economic_alternative[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        $weight_total = array();
        $max_weight = null;
        $alt_final_score = array(); //For each bench, add 50% if Business is Yes, and 50% is Alternative is Yes
        foreach ($item_subcat->economicrequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            $ratios = array();
            $weights = array();
            $max_value = null;
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (!isset($max_value) || $ratingeconomicrequests[$item_request->id]->value>$max_value) 
                    $max_value = $ratingeconomicrequests[$item_request->id]->value;
            }
            //With the max values for each request, now calculate ratios:
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                if ($max_value>0)
                    $ratios[$ratingbench->bench_id] = number_format(abs($ratingeconomicrequests[$item_request->id]->value-$max_value)*100/$max_value,0);
                else
                    $ratios[$ratingbench->bench_id] = 0;
                $weights[$ratingbench->bench_id] = $ratios[$ratingbench->bench_id] * $item_request->weight;
                if (isset($weight_total[$ratingbench->bench_id]))
                    $weight_total[$ratingbench->bench_id] += $weights[$ratingbench->bench_id];
                else
                    $weight_total[$ratingbench->bench_id] = $weights[$ratingbench->bench_id];

                if (!isset($max_weight) || $weight_total[$ratingbench->bench_id]>$max_weight)
                    $max_weight = $weight_total[$ratingbench->bench_id];
            }
            $this->m_economic_alternative[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "ratios" => $ratios,
                "weights" => $weights,
                "max_value" => $max_value,
                "unit" => "€"
            ];
        }

        //Subtotals:
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["title"] = "SUM";
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["weights"] = $weight_total;
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["max_weight"] = $max_weight;

        //Subcat scores:
        $result = array();
        foreach ($ratingbenches as $ratingbench) {
            if (!isset($alt_final_score[$ratingbench->bench_id]))
                $alt_final_score[$ratingbench->bench_id] = 0;

            if ($weight_total[$ratingbench->bench_id]>0.75*$max_weight) {
                $result[$ratingbench->bench_id] ="Yes";
                $alt_final_score[$ratingbench->bench_id]+=50;
            } else
                $result[$ratingbench->bench_id] ="No";
        }
        $this->m_economic_alternative[$SUBCAT_ID]["score"]["title"] = "BEST LAB FOR DELAYS";
        $this->m_economic_alternative[$SUBCAT_ID]["score"]["benches"] = $result;

        $SUBCAT_ID++;
        //CANCELLATION subcat:
        $item_subcat = $item_cat->economicsubcats()->where('title','like',"%Cancellation%")->first();
        $this->m_economic_alternative[$SUBCAT_ID]["title"] = $item_subcat->title;
        $bench_subtotals = array();
        $weight_total = array();
        $max_weight = null;
        foreach ($item_subcat->economicrequests()->orderby('ordering','asc')->get() as $item_request) {
            $values = array();
            $states = array();
            $ratios = array();
            $weights = array();
            $max_value = null;
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                $values[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->value;
                $states[$ratingbench->bench_id] = $ratingeconomicrequests[$item_request->id]->ratingrequeststate_id;
                if (!isset($max_value) || $ratingeconomicrequests[$item_request->id]->value>$max_value) 
                    $max_value = $ratingeconomicrequests[$item_request->id]->value;
            }
            //With the max values for each request, now calculate ratios:
            foreach ($ratingbenches as $ratingbench) {
                $ratingeconomicrequests = $ratingbench->ratingeconomicrequests()->get()->keyBy('economicrequest_id');
                if ($max_value>0)
                    $ratios[$ratingbench->bench_id] = number_format(abs($ratingeconomicrequests[$item_request->id]->value-$max_value)*100/$max_value,0);
                else
                    $ratios[$ratingbench->bench_id] = 0;

                $weights[$ratingbench->bench_id] = $ratios[$ratingbench->bench_id] * $item_request->weight;
                if (isset($weight_total[$ratingbench->bench_id]))
                    $weight_total[$ratingbench->bench_id] += $weights[$ratingbench->bench_id];
                else
                    $weight_total[$ratingbench->bench_id] = $weights[$ratingbench->bench_id];

                if (!isset($max_weight) || $weight_total[$ratingbench->bench_id]>$max_weight)
                    $max_weight = $weight_total[$ratingbench->bench_id];
            }
            if (!isset($max_weight) || $weight_total[$ratingbench->bench_id]>$max_weight)
                $max_weight = $weight_total[$ratingbench->bench_id];
            $this->m_economic_alternative[$SUBCAT_ID]["requests"][] = [
                "title" => $item_request->title,
                "values" => $values,
                "states" => $states,
                "ratios" => $ratios,
                "weights" => $weights,
                "max_value" => $max_value,
                "unit" => "%"
            ];
        }

        //dd(array_keys($bench_subtotals, min($bench_subtotals))); 
        //Subtotals:
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["title"] = "SUM";
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["unit"] = "%";
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["weights"] = $weight_total;
        $this->m_economic_alternative[$SUBCAT_ID]["subtotals"]["max_weight"] = $max_weight;

        //Subcat scores:
        $result = array();
        foreach ($ratingbenches as $ratingbench) {
            if ($weight_total[$ratingbench->bench_id]>0.75*$max_weight) {
                $result[$ratingbench->bench_id] ="Yes";
                $alt_final_score[$ratingbench->bench_id]+=50;
            } else
                $result[$ratingbench->bench_id] ="No";
        }
        $this->m_economic_alternative[$SUBCAT_ID]["score"]["title"] = "BEST LAB FOR CANCELLATION";
        $this->m_economic_alternative[$SUBCAT_ID]["score"]["benches"] = $result;

        //ALTERNATIVE CASE SCORE like a subcat:
        $SUBCAT_ID++;
        $this->m_economic_alternative[$SUBCAT_ID]["title"] = "Alternative case score";
        $this->m_economic_alternative[$SUBCAT_ID]["requests"][0]["title"] = "Total score";
        $this->m_economic_alternative[$SUBCAT_ID]["requests"][0]["values"] = $alt_final_score;
        $this->m_economic_alternative[$SUBCAT_ID]["requests"][0]["unit"] = "%";
        //dd($this->m_economic_alternative);

        //FINAL SCORE:
        $SUBCAT_ID=1;
        $alternative_scores = array();
        foreach ($ratingbenches as $ratingbench) {
            $alternative_scores[$ratingbench->bench_id] = (0.87*$business_ratios[$ratingbench->bench_id])+(0.13*$alt_final_score[$ratingbench->bench_id]);
        }
        $this->m_economic_finalscore[$SUBCAT_ID]["title"] = "Economic rating final score";
        $this->m_economic_finalscore[$SUBCAT_ID]["requests"][0]["title"] = "Total score";
        $this->m_economic_finalscore[$SUBCAT_ID]["requests"][0]["values"] = $alternative_scores;
        $this->m_economic_finalscore[$SUBCAT_ID]["requests"][0]["unit"] = "%";
        //dd($this->m_economic_finalscore);

        //Add a final score economiccat:
        $economiccat = new Economiccat();
        $economiccat->title = "Final Score";
        $economiccat->type = 3;
        $this->m_economic_cats[] = $economiccat;
        //dd($this->m_economic_cats);
    }

    //Export to excel full rating:
    public function store(Request $request) {
        $this->m_rating = Rating::find($request->rating_id);
        //Create excel file with full rating and store on server:
        $tech_file = "RATING_".$this->m_rating->id."_".time();
        ob_end_clean();
        ob_start();
        $excel_file = Excel::create($tech_file, function ($excel) {
            $this->technicalRating();
            $this->createInputdataSheet($excel);
            $this->createTechnicalSheet($excel);

            $this->timingRating();
            $this->createTimingSheet($excel);

            $this->economicsRating();
            $this->createEconomicsSheet($excel);

            //$this->createFinalSheet($excel);
        })->store('xlsx', "files/ratingtool");

        //Save on database, the store trazability:
        $ratingfile = new Ratingfile();
        $ratingfile->title = $request->title;
        $ratingfile->description = $request->description;
        $ratingfile->rating_id = $this->m_rating->id;
        $ratingfile->file = $excel_file->filename.".".$excel_file->ext;
        $ratingfile->save();
        return redirect()->route('ratingfiles.index',['area_id'=>$this->m_rating->area_id]);        
    }

    //------------------AUXILIAR FUNCTIONS------------------------------:

    //Benches for a rating:
    private function ratingBenches($rating) {
        //score -> The rating score for the bench, will be calculated later, as the sum of each techrequest score of each techcat applicable, and depends on criticality score of the techrequest (P/S/T), and the criteriafunc.
        //result -> will be calculated later, -1 NOGO, 0->CAUTION , 1->GO (by default, we suppose GO result for the bench, and will be update with CAUTION or NOGO, during the techrequest review)
        $this->m_benches = DB::table('benches')
            ->select('benches.id', 'benches.title', DB::raw('0 as score'), DB::raw('1 as result'))
            ->join('ratingbenches', 'benches.id', '=', 'ratingbenches.bench_id')
            ->where('ratingbenches.rating_id','=',$rating->id)
            //->orderby('benches.id','asc')
            ->get()
            ->keyBy('id');
    }

    //Technical: Get the techcats applicables for the rating:
    private function getTechcatsApplicables($rating_id) {
        $this->m_techcats_applicables = DB::table('techcats')
            ->select('techcats.id', 'techcats.title',DB::raw('0 as criticalities_totals'),DB::raw('0 as score_total'))
            ->join('ratingtechcats', 'techcats.id', '=', 'ratingtechcats.techcat_id')
            ->where(['ratingtechcats.rating_id' => $rating_id, 'ratingtechcats.applicable_id' => '1'])
            ->orderby('techcats.id','asc')
            ->get()
            ->keyBy('id');
    }

    //Technical: Get totals criticalities for an applicable techcat of a rating:
    private function criticalitiesTotalScores($rating_id,$techsheet_id,$techcats) {
        //Implement this query to calculate score1: 
        /*
        $cats = implode (", ", $techcats);
        $sql = "SELECT ratingtechrequests.criticality_id,count(*) as total,criticality_techsheet.score_weight*0.001/count(*) as score FROM ratingtechrequests INNER JOIN criticality_techsheet ON ratingtechrequests.criticality_id = criticality_techsheet.criticality_id INNER JOIN techrequests ON techrequests.id=ratingtechrequests.techrequest_id where rating_id=$rating_id and criticality_techsheet.techsheet_id=$techsheet_id and techrequests.techcat_id in ($cats) group by criticality_techsheet.criticality_id,criticality_techsheet.score_weight order by criticality_id Asc";
        dd($sql);
        */
        //Get ratingtechcats applicables:
        $this->m_criticalities_total = DB::table('ratingtechrequests')
            ->select('ratingtechrequests.criticality_id', DB::raw('count(*) as total'), DB::raw('ROUND(criticality_techsheet.score_weight*0.001/count(*),4) as score'))
            ->join('criticality_techsheet', 'ratingtechrequests.criticality_id', '=', 'criticality_techsheet.criticality_id')
            ->join('techrequests', 'techrequests.id', '=','ratingtechrequests.techrequest_id')
            ->where('rating_id','=',$rating_id)
            ->where('criticality_techsheet.techsheet_id','=',$techsheet_id)
            ->whereIn('techrequests.techcat_id',$techcats)
            ->groupBy('ratingtechrequests.criticality_id','criticality_techsheet.score_weight')
            ->orderby('criticality_id','asc')
            ->get()
            ->keyBy('criticality_id');

        /*
        //DB raw option:
        $criticalities_totals = DB::select($sql);
        */
    }
    //Technical: Count items criticalities (P/S/T) for a techcat
    private function criticalitiesTechcatTotals($rating_id,$techsheet_id,$techcat_id) {
        return DB::table('ratingtechrequests')
            ->select('ratingtechrequests.criticality_id', DB::raw('count(*) as total'),DB::raw('0 as score'))
            ->join('criticality_techsheet', 'ratingtechrequests.criticality_id', '=', 'criticality_techsheet.criticality_id')
            ->join('techrequests', 'techrequests.id', '=','ratingtechrequests.techrequest_id')
            ->where('rating_id','=',$rating_id)
            ->where('criticality_techsheet.techsheet_id','=',$techsheet_id)
            ->where('techrequests.techcat_id','=',$techcat_id)
            ->groupBy('ratingtechrequests.criticality_id')
            ->orderby('criticality_id','asc')
            ->get()
            ->keyBy('criticality_id');
    }


	//Technical: Evaluate a bench for a requirement:
    private function getCriteriafuncResult($techrequest,$bench_title,$bench_value,$inputdata) {
        /*
        if ($this->log_dev) Log::channel('custom')->info("getCriteriafuncResult($techrequest->id,$techrequest->criteriafunc_id,$bench_value,$inputdata,$techrequest->$inputfactor,$techrequest->$value,$techrequest->$range_x,$techrequest->$range_y): ");
        */
        //Check if apply input factor:
        if (isset($techrequest->inputfactor)) $inputdata = $inputdata * $techrequest->inputfactor;
        $score = (-1)*$techrequest->criteriafunc_id;    //In case of error, to know the criteria func case
        try {
            switch ($techrequest->criteriafunc_id) {
                case 1: //Binary: DB >= ID
                    if ($bench_value>=$inputdata) $score=1; else $score=0;
                    break;
                case 2: //Binary: DB <= ID
                    if ($bench_value<=$inputdata) $score=1; else $score=0;
                    break;
                case 3: //Binary: DB >= Value
                    if ($bench_value>=$techrequest->value) $score=1; else $score=0;
                    break; 
                case 4: //Binary: DB <= Value
                    if ($bench_value<=$techrequest->value) $score=1; else $score=0;
                    break;
                case 5: //Binary: DB = Yes
                    if ($bench_value=="Yes") $score=1; else $score=0;
                    break;
                case 6: //Binary: ID=Yes && DB=No
                    if ($bench_value=="Yes" || ($inputdata=="No" && $bench_value=="No")) $score=1; else $score=0;
                    break;
                case 7: //Scale: DB Content (It would be 0, 1 or 2)
                    if (is_numeric($bench_value) && ($bench_value==0 || $bench_value==1 || $bench_value==2)) {
                        $score=$bench_value;
                    } else {
                        throw new Exception("bench_value: ($bench_value) on ($bench_title) is not in [0,1,2] range",1);
                    }
                    break;
                case 8: //Scale: DB Range X-Y
                    if ($bench_value<$techrequest->range_x) $score=0;
                    if ($bench_value>=$techrequest->range_x && $bench_value<=$techrequest->range_y) $score=1;
                    if ($bench_value>$techrequest->range_y) $score=2;
                    break;
                case 9: //Scale: DB vs ID (Numeric values)
                    if ($bench_value<$inputdata) $score=0;
                    if ($bench_value==$inputdata) $score=1;
                    if ($bench_value>$inputdata) $score=2;
                    break;
                case 10: //Scale: DB vs ID*Range X-Y
                    if ($bench_value<$techrequest->range_x*$inputdata) $score=0;
                    if (($techrequest->range_x*$inputdata<=$bench_value) && ($bench_value<=$techrequest->range_y*$inputdata)) $score=1;
                    if ($bench_value>$techrequest->range_y*$inputdata) $score=2;
                    break;
                case 11: //Scale: DB vs ID (Yes/No conditions)
                    if ($bench_value=="No") $score=0;
                    if ($inputdata=="No" && $bench_value=="Yes") $score=1;
                    if ($inputdata=="Yes" && $bench_value=="Yes") $score=2;
                    break;
                case 12: //Scale: DB < Range X-Y (Numeric values)
                    if ($bench_value<$techrequest->range_x) $score=2;
                    if ($bench_value>=$techrequest->range_x && $bench_value<=$techrequest->range_y) $score=1;
                    if ($bench_value>$techrequest->range_y) $score=0;
                    break;
                case 13: //Binary: DB == ID (Numeric)
                    if ($bench_value==$inputdata) $score=1; else $score=0;
                    break;
                default:
                    $score = -1000;    //invalid value
                    break;
            }
        } catch (Exception $ex) {
            Log::channel('custom')->info("getCriteriafuncResult($techrequest->id,$techrequest->criteriafunc_id,$bench_value,$inputdata,$techrequest->inputfactor,$techrequest->value,$techrequest->range_x,$techrequest->range_y): ".$ex->getMessage());
        }
        if ($this->log_dev) Log::channel('custom')->info("Score: $score");
        return $score;
    }
}
