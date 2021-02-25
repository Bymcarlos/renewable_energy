<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Partner;
use CtoVmm\Scope;
use CtoVmm\Generalsheet;
use CtoVmm\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($scope_id)
    {
        $scope = Scope::find($scope_id);
        $partners = Partner::where('scope_id','=',$scope_id)->get();
        return view('partners.partners')
            ->with('scope',$scope)
            ->with('partners',$partners);
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
        $partner = new Partner();
        $partner->scope_id = $request->scope_id;
        $partner->title = $request->title;
        $partner->nda = $request->nda;
        if (isset($request->description))
            $partner->description = $request->description;
        $partner->save();
        //Associate a partner sheet (Generalsheet with ID->1):
        $sheet = Generalsheet::find(1);
        $partner->generalsheets()->attach($sheet);
        //And the requests of the sheet:
        foreach ($sheet->sections()->get() as $section) {
            foreach ($section->generalrequests()->get() as $generalrequest) {
                $partner->generalrequests()->attach($generalrequest);
            }
        }
        return redirect()->route('partners.index',['scope'=>$partner->scope_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Partner $partner)
    {
        $partner->title = $request->title;
        $partner->nda = $request->nda;
        $partner->description = $request->description;
        $partner->update();
        return redirect()->route('partners.index',['scope'=>$partner->scope_id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->route('partners.index',['scope'=>$partner->scope_id]);
    }

    public function scopes() {
        $scopes = Scope::all();
        return view('partners.scopes')
            ->with('scopes',$scopes);
    }

    public function sheet($partner_id,$sheet_id,$section_id=null) {
        $partner = Partner::find($partner_id);
        $sheet = Generalsheet::find($sheet_id);
        if (isset($section_id)) 
            $section = Section::find($section_id);
        else
            $section = $sheet->sections()->first();
        //Requests values fot the sheet and partner:
        $partner_generalrequests =  DB::table('generalrequest_partner')
            ->select('partner_id', 'generalrequest_id', 'value')
            ->where('partner_id','=',$partner_id)
            ->get()
            ->keyBy('generalrequest_id');

        return view('partners.sheet')
            ->with('partner',$partner)
            ->with('sheet',$sheet)
            ->with('section',$section)
            ->with('partner_generalrequests',$partner_generalrequests);
    }

    public function setValue(Request $request) {
        $partner = Partner::find($request->partner_id);
        $generalsheet = Generalsheet::find($request->sheet_id);
        $section = Section::find($request->section_id);
        $partner->generalrequests()->updateExistingPivot($request->generalrequest_id,['value'=>$request->value]);

        return redirect()->route('partner.sheet',['partner_id'=>$partner->id,'sheet_id'=>$generalsheet->id,'setion_id'=>$section->id]);
    }
}
