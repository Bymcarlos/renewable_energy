<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Ratingfile;
use CtoVmm\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;

class RatingfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($area_id)
    {
        $area = Area::find($area_id);
        $ratingfiles = DB::table('ratingfiles')
            ->select('ratingfiles.id', 'ratingfiles.title','ratingfiles.description','ratingfiles.file','ratingfiles.status','ratingfiles.created_at')
            ->join('ratings', 'ratingfiles.rating_id', '=', 'ratings.id')
            ->where('ratings.area_id','=',$area_id)
            //->orderby('ratingfiles.id','asc')
            ->get();
        return view('ratingtools.reports.list')
            ->with('area',$area)
            ->with('ratingfiles',$ratingfiles);
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
     * @param  \CtoVmm\Ratingfile  $ratingfile
     * @return \Illuminate\Http\Response
     */
    public function show(Ratingfile $ratingfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Ratingfile  $ratingfile
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratingfile $ratingfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Ratingfile  $ratingfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratingfile $ratingfile)
    {
        $ratingfile->title = $request->title;
        $ratingfile->description = $request->description;
        $ratingfile->update();
        return redirect()->route('ratingfiles.index',['area_id'=>$request->area_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Ratingfile  $ratingfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratingfile $ratingfile)
    {
        //Ara:
        $area_id = $ratingfile->rating()->first()->area_id;
        //Check status:
        if ($ratingfile->status==1) {
            //Remove file from server:
            $file_path = "files/ratingtool/".$ratingfile->file;
            if (File::exists($file_path)) {
                if (File::delete($file_path)) {
                    $ratingfile->delete();
                }
            } else
                $ratingfile->delete();
        }
        return redirect()->route('ratingfiles.index',['area_id'=>$area_id]);
    }

    public function statusChange(Request $request) {
        $ratingfile =Ratingfile::find($request->id);
        $ratingfile->status = 2;
        $ratingfile->update();
        return redirect()->route('ratingfiles.index',['area_id'=>$request->area_id]);
    }

    public function areas() {
        $areas = Area::all();
        return view('ratingtools.reports.areas')
            ->with('areas',$areas);
    }
}
