<?php

namespace CtoVmm\Http\Controllers;

use CtoVmm\Scope;
use Illuminate\Http\Request;

class ScopeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scopes = Scope::all();
        return view('partners.templates.scopes')
            ->with('scopes',$scopes);
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
        $scope = new Scope();
        $scope->title = $request->title;
        if (isset($request->description))
            $scope->description = $request->description;
        $scope->save();
        return redirect()->route('scopes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \CtoVmm\Scope  $scope
     * @return \Illuminate\Http\Response
     */
    public function show(Scope $scope)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \CtoVmm\Scope  $scope
     * @return \Illuminate\Http\Response
     */
    public function edit(Scope $scope)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CtoVmm\Scope  $scope
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Scope $scope)
    {
        $scope->title = $request->title;
        $scope->description = $request->description;
        $scope->update();
        return redirect()->route('scopes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CtoVmm\Scope  $scope
     * @return \Illuminate\Http\Response
     */
    public function destroy(Scope $scope)
    {
        $scope->delete();
        return redirect()->route('scopes.index');
    }
}
