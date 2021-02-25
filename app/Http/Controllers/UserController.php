<?php

namespace CtoVmm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use CtoVmm\User;
use CtoVmm\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('intranet.users')
            ->with('users',$users)
            ->with('roles',$roles);
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
        $user = new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->save();

        $role = Role::find($request->role_id);
        $user->roles()->attach($role);
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->update();

        $user->roles()->detach();
        $role = Role::find($request->role_id);
        $user->roles()->attach($role);
        return redirect()->route('users.index');
    }

    public function passwordChange(Request $request, $id) {
        $user = User::find($id);
        $user->password=Hash::make($request->password);
        $user->update();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        //User Admin can not be removed:
        if ($user->id>1) {
            $user->delete();
        }
        return redirect()->route('users.index');
    }

    public function forgotPassword() {
        //Get Admin user list:
        $users = Role::find(1)->users()->get();
        return view('auth.forgot')
            ->with('users',$users);
    }
}
