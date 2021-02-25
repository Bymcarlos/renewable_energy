<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use CtoVmm\Bench;
use CtoVmm\Sheet;
use CtoVmm\Feature;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function benches_sheets () {
    	//$bench = Bench::find($bench_id);
    	$total=0;
    	$benches = Bench::all();
    	foreach ($benches as $bench) {
    		$sheet_sp = $bench->areaComponent()->first()->component()->first()->sheet()->first();
    		echo "<p><strong>Bench ($bench->id):$bench->title [Specific -> Sheet ($sheet_sp->id):$sheet_sp->title]</strong></p>";
	    	foreach ($bench->sheets()->get() as $sheet) {
	    		if ($sheet->required==2 && $sheet->id!=$sheet_sp->id) {
	    			echo "<p>&nbsp;&nbsp;&nbsp;Sheet ($sheet->id):$sheet->title</p>";
	    			//echo "delete from bench_sheet where bench_id='$bench->id' and sheet_id='$sheet->id';<br/>";
	    			/*
	    			foreach($sheet->cats()->get() as $cat) {
		                foreach($cat->subcats()->get() as $subcat) {
		                    foreach ($subcat->questions()->get() as $question) {
		                        foreach ($question->features()->get() as $feature) {
		                            if ($bench->features()->where('feature_id', $feature->id)->exists()) {
		                            	$bench_feature = $bench->features()->where('feature_id', $feature->id)->first();
		                            	echo "delete from bench_feature where bench_id='$bench->id' and feature_id='$feature->id';<br/>";
		                            	if (isset($bench_feature->value) && (strlen($bench_feature->value)>0)) {
		                            		echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feature ($feature->id):$feature->id->title [value->$bench_feature->value]</p>";
		                            		echo "<p>ERRORRRRRRR!!!</p>"; exit;
		                            		$total++;
		                            	}
		                            }
		                        }
		                    }
		                }
		            }
		            */
	    		}
	    	}
	    }
	    //echo "<h2>TOTAL: $total</h2>";
    }

    private function benches_sheets_list () {
    	$total=0;
    	$benches = Bench::where('benchtype_id','=','1')->get();
    	foreach ($benches as $bench) {
    		$sheet_sp = $bench->areaComponent()->first()->component()->first()->sheet()->first();
    		echo "<p><strong>Bench ($bench->id):$bench->title [Specific -> Sheet ($sheet_sp->id):$sheet_sp->title]</strong></p>";
	    	foreach ($bench->sheets()->get() as $sheet) {
	    		if ($sheet->required==2 && $sheet->id!=$sheet_sp->id) {
	    			echo "<p>&nbsp;&nbsp;&nbsp;Sheet ($sheet->id):$sheet->title</p>";
	    			/*
	    			foreach($sheet->cats()->get() as $cat) {
		                foreach($cat->subcats()->get() as $subcat) {
		                    foreach ($subcat->questions()->get() as $question) {
		                        foreach ($question->features()->get() as $feature) {
		                            if ($bench->features()->where('feature_id', $feature->id)->exists()) {
		                            	$bench_feature = $bench->features()->where('feature_id', $feature->id)->first();
		                            	if (isset($bench_feature->value) && (strlen($bench_feature->value)>0)) {
		                            		echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feature ($feature->id):$feature->id->title [value->$bench_feature->value]</p>";
		                            		$total++;
		                            	}
		                            }
		                        }
		                    }
		                }
		            }
		            */
	    		}
	    	}
	    }
	    //echo "<h2>TOTAL: $total</h2>";
    }

    private function bench_features_specific_sheet($bench_id) {
    	$items = DB::table('bench_feature')
            ->select(DB::raw('*'))
            ->where('bench_id','=',$bench_id)
            ->get();
        foreach ($items as $item) {
        	$feature = Feature::find($item->feature_id);
        	$sheet = $feature->question()->first()->subcat()->first()->cat()->first()->sheet()->first();
        	if ($sheet->required==2)
        		echo "Sheet: ".$sheet->title."<br/>";
        }

    }
}
