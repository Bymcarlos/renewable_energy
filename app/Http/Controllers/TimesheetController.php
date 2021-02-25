<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Timesheet;
use CtoVmm\Timecat;
use CtoVmm\Timesubcat;
use CtoVmm\Timerequest;
use CtoVmm\Timerequestsett;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timesheets = Timesheet::all();
        $timesheets_state = array();
        //Check if timesheets has request with state -1 (pending of user review)
        foreach ($timesheets as $timesheet) {
            $timesheets_state[$timesheet->id] = DB::table('timerequests')
                ->select(DB::raw('count(*) as pending_review'))
                ->join('timesubcats', 'timesubcats.id', '=', 'timerequests.timesubcat_id')
                ->join('timecats', 'timecats.id', '=', 'timesubcats.timecat_id')
                ->join('timesheets', 'timesheets.id', '=', 'timecats.timesheet_id')
                ->where('timesheets.id',$timesheet->id)
                ->where('timerequests.state','<',0)
                ->first();
        }
        //dd($timesheets_state);
        return view('ratingtools.templates.time.list')
            ->with('timesheets',$timesheets)
            ->with('timesheets_state',$timesheets_state);
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
        $timesheet = new Timesheet();
        $timesheet->title = $request->title;
        $timesheet->description = $request->description;
        $timesheet->save();

        //Check if new empty template or create from other:
        if (isset($request->timesheet_id) && $request->timesheet_id>0) {
            $timesheet_from = Timesheet::find($request->timesheet_id);
            //Create same categories with same requests:
            foreach ($timesheet_from->timecats()->get() as $timecat_from) {
                $timecat = new Timecat();
                $timecat->title = $timecat_from->title;
                $timecat->type = $timecat_from->type;
                $timecat->score_weight = $timecat_from->score_weight;
                $timecat->timesheet_id = $timesheet->id;
                $timecat->save();
                //Now subcats:
                foreach ($timecat_from->timesubcats()->get() as $timesubcat_from) {
                    $timesubcat = new Timesubcat();
                    $timesubcat->title = $timesubcat_from->title;
                    $timesubcat->timecat_id = $timecat->id;
                    $timesubcat->save();

                    //Now al requests:
                    foreach ($timesubcat_from->timerequests()->get() as $timerequest_from) {
                        $timerequest = new Timerequest();
                        $timerequest->title = $timerequest_from->title;
                        $timerequest->label = $timerequest_from->label;
                        $timerequest->ordering = $timerequest_from->ordering;
                        $timerequest->settable = $timerequest_from->settable;
                        $timerequest->state = $timerequest_from->state;
                        $timerequest->timesubcat_id = $timesubcat->id;
                        $timerequest->save();
                        if ($timerequest_from->settable>0) {
                            foreach ($timerequest_from->timerequestsetts()->get() as $timerequestsett_from) {
                                $timerequestsett = new Timerequestsett();
                                $timerequestsett->percent = $timerequestsett_from->percent;
                                $timerequestsett->value = $timerequestsett_from->value;
                                $timerequestsett->label = $timerequestsett_from->label;
                                $timerequestsett->timerequest_id = $timerequest->id;
                                $timerequestsett->save();
                            }
                        }
                    }
                }
            }
        } else {
            //Create cats and subcats structure:
            $timecat = Timecat::create(['title'=>'Availability','type'=>1,'score_weight'=>50,'timesheet_id'=>$timesheet->id]);
                $timesubcat = Timesubcat::create(['title'=>'General','administrable'=>false,'timecat_id'=>$timecat->id]);
                $timerequest = Timerequest::create(['title'=>'Availability percentage','ordering'=>1,'settable'=>1,'state'=>-1,'timesubcat_id'=>$timesubcat->id]);
                    Timerequestsett::create(['percent'=>'100','value'=>'0','label'=>'weeks','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'80','value'=>'2','label'=>'weeks','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'60','value'=>'4','label'=>'weeks','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'40','value'=>'6','label'=>'weeks','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'20','value'=>'8','label'=>'weeks','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'0','value'=>'10','label'=>'weeks','timerequest_id'=>$timerequest->id]);

            $timecat = Timecat::create(['title'=>'Test execution time','type'=>2,'score_weight'=>30,'timesheet_id'=>$timesheet->id]);
                $timesubcat = Timesubcat::create(['title'=>'Laboratory execution times','timecat_id'=>$timecat->id]);
                Timerequest::create(['title'=>'Test specimen arrival, inspection','ordering'=>1,'timesubcat_id'=>$timesubcat->id]);

                $timesubcat = Timesubcat::create(['title'=>'Incidents','timecat_id'=>$timecat->id]);
                    Timerequest::create(['title'=>'Weather related stops','ordering'=>1,'timesubcat_id'=>$timesubcat->id]);
                    Timerequest::create(['title'=>'Holidays','ordering'=>2,'timesubcat_id'=>$timesubcat->id]);

                $timesubcat = Timesubcat::create(['title'=>'Transportation times','timecat_id'=>$timecat->id]);
                Timerequest::create(['title'=>'Test specimen transportation','ordering'=>1,'timesubcat_id'=>$timesubcat->id]);

            $timecat = Timecat::create(['title'=>'Flexibility','type'=>3,'score_weight'=>20,'timesheet_id'=>$timesheet->id]);
                $timesubcat = Timesubcat::create(['title'=>'General','administrable'=>false,'timecat_id'=>$timecat->id]);
                $timerequest = Timerequest::create(['title'=>'Flexibility percentage','label'=>'%','ordering'=>1,'settable'=>2,'timesubcat_id'=>$timesubcat->id]);
                    Timerequestsett::create(['percent'=>'100','value'=>'100','label'=>'Total flexibility','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'80','value'=>'80','label'=>'Very good','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'60','value'=>'60','label'=>'Fair','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'40','value'=>'40','label'=>'Low','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'20','value'=>'20','label'=>'Very low','timerequest_id'=>$timerequest->id]);
                    Timerequestsett::create(['percent'=>'0','value'=>'0','label'=>'No flexibility','timerequest_id'=>$timerequest->id]);
        }

        

        return redirect()->route('timesheets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function show(Timesheet $timesheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timesheet $timesheet)
    {
        $timesheet->title=$request->title;
        $timesheet->description=$request->description;
        $timesheet->update();
        return redirect()->route('timesheets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();
        return redirect()->route('timesheets.index');
    }

    public function getCategoriesWeight($timesheet_id) {
        $timesheet = Timesheet::find($timesheet_id);
        $timecats = $timesheet->timecats()->get();
        return Response::json($timecats);
    }

    public function setCategoriesWeight(Request $request, $timesheet_id) {
        $timesheet = Timesheet::find($timesheet_id);
        $timecats = $timesheet->timecats()->get();
        foreach ($timecats as $timecat) {
            $field = "weight_".$timecat->id;
            $timecat->score_weight = $request->$field;
            $timecat->update();
        }
        return redirect()->route('timesheets.index');
    }
}
