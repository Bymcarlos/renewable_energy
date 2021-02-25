<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use CtoVmm\Area;
use CtoVmm\Sheet;
use CtoVmm\Component;

//Export to excel:
use Maatwebsite\Excel\Facades\Excel;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = Area::all();
        $sheets = Sheet::all()->sortBy("title");
        $components = Component::all();
        return view('intranet.components')
            ->with('areas',$areas)
            ->with('sheets',$sheets)
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
        $component = new Component();
        $component->title = $request->title;
        $component->sheet_id = $request->sheet_id;
        $component->save();
        //Check selected areas:
        $areas = Area::all();
        foreach ($areas as $area) {
            $check_field = "area_".$area->id;
            if ($request[$check_field]) {
                $area->components()->attach($component);
            }
        }
        return redirect()->route('components.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function show(Component $component)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function edit(Component $component)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Component $component)
    {
        //dd($request);
        $component->title = $request->title;
        $component->update();
        //Check selected areas:
        $areas = Area::all();
        foreach ($areas as $area) {
            $check_field = "area_".$area->id;
            //Check if is attached or not:
            $attached = $area->components->contains($component);
            if ($request[$check_field]) {
                if (!$attached)
                    $area->components()->attach($component);
            } else {
                if ($attached)
                    $area->components()->detach($component);
            }
        }
        return redirect()->route('components.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function destroy(Component $component)
    {
        //Remove area/component related items
        $component->areas()->detach();
        $component->delete();
        return redirect()->route('components.index');
    }

    public function list(Request $request) {
        $component = Component::find($request->component_id);
        $areas = $component->areas()->get();
        return Response::json($areas);
    }

    public function Exc_Components() {
        $list = array();
        $components = Component::all()->sortBy('title');
        foreach ($components as $component) {
            $areas ="";
            foreach ($component->areas()->get() as $area) {
                if (strlen($areas)==0)
                    $areas=$area->title;
                else
                    $areas.=" - ".$area->title;
            }
            $list[] = [
                'COMPONENT' => $component->title,
                'SHEET' => $component->sheet()->first()->title,
                'AREAS' => $areas,
            ];
        }
        ob_end_clean();
        ob_start();
        Excel::create('Components', function ($excel) use ($list) {
            $excel->sheet('Components', function ($sheet) use ($list) {
                $sheet->setPageMargin(array(0.5, 0.25, 0.4, 0.30)); //top, right, bottom, left
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Verdana',
                        'size'      =>  9
                    )
                ));
                $sheet->with($list, null, 'A1', false, false);
                $sheet->cells("A1:C1", function($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#afffe0');
                });
            });
        })->download('xlsx');
    }
}
