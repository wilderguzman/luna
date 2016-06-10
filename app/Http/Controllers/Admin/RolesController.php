<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

use App\Permission;


class RolesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        
        $roles = Role::with('permissions')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $permissions = Permission::lists('display_name', 'id');
        
        return view('admin.roles.create' , compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'display_name' => 'required', ]);

        $roles=Role::create($request->all());
        
        $roles->attachPermissions($request->input('permission_id'));

        Session::flash('flash_message', 'Role added!');

        return redirect('admin/roles');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $permission_role = Role::find($id)->permissions()->lists('permission_id')->toArray();

        $permissions = Permission::lists('display_name', 'id');

        return view('admin.roles.edit', compact('role', 'permissions', 'permission_role'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, ['name' => 'required', 'display_name' => 'required', ]);

        $role = Role::findOrFail($id);
        $role->update($request->all());


        if($role->permissions->count()) {

               $role->permissions()->detach($role->permissions()->lists('permission_id')->toArray());
            }

        $role->attachPermissions($request->input('permission_id'));

        Session::flash('flash_message', 'Role updated!');

        return redirect('admin/roles');

           

            



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        Role::destroy($id);

        Session::flash('flash_message', 'Role deleted!');

        return redirect('admin/roles');
    }

}
