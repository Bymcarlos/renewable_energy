<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Economicsheet;
use CtoVmm\Economiccat;
use CtoVmm\Economicsubcat;
use CtoVmm\Economicrequest;
use Illuminate\Http\Request;

class EconomicsheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $economicsheets= Economicsheet::all();
        return view('ratingtools.templates.economic.list')
            ->with('economicsheets',$economicsheets);
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
        $economicsheet = new Economicsheet();
        $economicsheet->title = $request->title;
        $economicsheet->description = $request->description;
        $economicsheet->save();
        //Check if new empty template or create from other:
        if (isset($request->economicsheet_id) && $request->economicsheet_id>0) {
            //New from other:
            $economicsheet_from = Economicsheet::find($request->economicsheet_id);
            foreach ($economicsheet_from->economiccats()->get() as $economiccat_from) {
                $economiccat = new Economiccat();
                $economiccat->title = $economiccat_from->title;
                $economiccat->type = $economiccat_from->type;
                $economiccat->economicsheet_id = $economicsheet->id;
                $economiccat->save();
                foreach ($economiccat_from->economicsubcats()->get() as $economicsubcat_from) {
                    $economicsubcat = new Economicsubcat();
                    $economicsubcat->title = $economicsubcat_from->title;
                    $economicsubcat->administrable = $economicsubcat_from->administrable;
                    $economicsubcat->weighted = $economicsubcat_from->weighted;
                    $economicsubcat->economiccat_id = $economiccat->id;
                    $economicsubcat->save();
                    foreach ($economicsubcat_from->economicrequests()->get() as $economicrequest_from) {
                        $economicrequest = new Economicrequest();
                        $economicrequest->title = $economicrequest_from->title;
                        $economicrequest->help = $economicrequest_from->help;
                        $economicrequest->ordering = $economicrequest_from->ordering;
                        $economicrequest->weight = $economicrequest_from->weight;
                        $economicrequest->unit_id = $economicrequest_from->unit_id;
                        $economicrequest->economicsubcat_id = $economicsubcat->id;
                        $economicrequest->save();
                    }
                }
            }
        } else {    
            //New empty template: Create cats and subcats default structure:
            $economiccat = Economiccat::create(['title'=>'Business case','type'=>1,'economicsheet_id'=>$economicsheet->id]);
                $economicsubcat = Economicsubcat::create(['title'=>'Capex','economiccat_id'=>$economiccat->id]);
                Economicrequest::create(['title'=>'Adaptor','ordering'=>1,'economicsubcat_id'=>$economicsubcat->id]);

                $economicsubcat = Economicsubcat::create(['title'=>'Opex','economiccat_id'=>$economiccat->id]);
                Economicrequest::create(['title'=>'Consumables','ordering'=>1,'economicsubcat_id'=>$economicsubcat->id]);
                Economicrequest::create(['title'=>'Execution of Test (including Man Hours)','ordering'=>2,'economicsubcat_id'=>$economicsubcat->id]);

                $economicsubcat = Economicsubcat::create(['title'=>'Transportation and others','economiccat_id'=>$economiccat->id]);
                Economicrequest::create(['title'=>'Transport to Lab','ordering'=>1,'economicsubcat_id'=>$economicsubcat->id]);
                Economicrequest::create(['title'=>'Trips & Expenses','help'=>'(2 SGRE test engineers)','ordering'=>2,'economicsubcat_id'=>$economicsubcat->id]);

                $economicsubcat = Economicsubcat::create(['title'=>'Opportunity cost','economiccat_id'=>$economiccat->id]);
                Economicrequest::create(['title'=>'Internal test cost','ordering'=>1,'economicsubcat_id'=>$economicsubcat->id]);

            $economiccat = Economiccat::create(['title'=>'Alternative case','type'=>2,'economicsheet_id'=>$economicsheet->id]);
                $economicsubcat = Economicsubcat::create(['title'=>'Delays, failuring during test','weighted'=>true,'economiccat_id'=>$economiccat->id]);
                $orden=0;
                //Default requests:
                Economicrequest::create([
                    'title'=>'Extra-week of test rig occupation',
                    'help'=>'(€/week)',
                    'ordering'=>1,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>0.5
                ]);
                Economicrequest::create([
                    'title'=>'Extra working hour: Enginee',
                    'help'=>'(€/hour)',
                    'ordering'=>2,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>0.15
                ]);
                Economicrequest::create([
                    'title'=>'Extra working hour: Technician',
                    'help'=>'(€/hour)',
                    'ordering'=>3,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>0.15
                ]);
                Economicrequest::create([
                    'title'=>'Storage',
                    'help'=>'(€/week)',
                    'ordering'=>4,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>0.2
                ]);

                $economicsubcat = Economicsubcat::create(['title'=>'Cancellation','administrable'=>false,'economiccat_id'=>$economiccat->id]);
                $orden=0;
                Economicrequest::create([
                    'title'=>'Cancellation no later than X months before test start',
                    'help'=>'(% of final price charged)',
                    'ordering'=>++$orden,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>1,
                    'unit_id'=>58
                ]);
                Economicrequest::create([
                    'title'=>'Cancellation no later than Y months before test start',
                    'help'=>'(% of final price charged)',
                    'ordering'=>++$orden,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>1,
                    'unit_id'=>58
                ]);
                Economicrequest::create([
                    'title'=>'Other penalties',
                    'help'=>'(% of final price charged)',
                    'ordering'=>++$orden,
                    'economicsubcat_id'=>$economicsubcat->id,
                    'weight'=>1,
                    'unit_id'=>58
                ]);
        }
        return redirect()->route('economicsheets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Economicsheet  $economicsheet
     * @return \Illuminate\Http\Response
     */
    public function show(Economicsheet $economicsheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Economicsheet  $economicsheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Economicsheet $economicsheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Economicsheet  $economicsheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Economicsheet $economicsheet)
    {
        $economicsheet->title = $request->title;
        $economicsheet->description = $request->description;
        $economicsheet->update();

        return redirect()->route('economicsheets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Economicsheet  $economicsheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Economicsheet $economicsheet)
    {
        $economicsheet->delete();
        return redirect()->route('economicsheets.index');
    }
}
