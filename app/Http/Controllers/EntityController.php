<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Entity;

//Export to excel:
use Maatwebsite\Excel\Facades\Excel;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = Entity::all();
        return view('intranet.entities')->with('entities',$entities);
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
        $entity = new Entity();
        $entity->title = strtoupper($request->title);
        $entity->save();
        return redirect()->route('entities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function show(Entity $entity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function edit(Entity $entity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entity $entity)
    {
        $entity->title = strtoupper($request->title);
        $entity->update();
        return redirect()->route('entities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entity $entity)
    {
        //TODO: Check if exist benches?
        $entity->delete();
        return redirect()->route('entities.index');
    }

    public function Exc_Entities() {
        $list = array();
        $entities = Entity::all();
        foreach ($entities as $entity) {
            $list[] = [
                'Entity' => $entity->title,
            ];
        }
        ob_end_clean();
        ob_start();
        Excel::create('Entities', function ($excel) use ($list) {
            $excel->sheet('Entities', function ($sheet) use ($list) {
                $sheet->with($list, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
