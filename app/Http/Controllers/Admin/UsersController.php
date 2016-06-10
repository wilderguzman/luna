<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use App\Role;
use DB;


class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
   
    public function index()
    {


        $users = User::with('roles')->get();

        return view('admin.users.index', compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        $roles = Role::orderBy('display_name', 'asc')->lists('display_name', 'id');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'email' => 'required', 'password' => 'required', ]);

       $user = User::create($request->all());

        $user->password=(bcrypt($user->password));
        
       $user->save();
       $user->attachRoles($request->input('role_id'));

        Session::flash('flash_message', 'User added!');


        return redirect('admin/users');
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
        $user = User::findOrFail($id);

        $roles = Role::orderBy('display_name', 'asc')->lists('display_name', 'id');
        return view('admin.users.show', compact('user','roles'));
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


        $user = User::findOrFail($id);

        $roles_user = User::find($id)->roles()->lists('role_id')->toArray();

        $roles = Role::orderBy('display_name', 'asc')->lists('display_name', 'id');
        

        return view('admin.users.edit', compact('user' , 'roles', 'roles_user'));
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
        $this->validate($request, ['name' => 'required', 'email' => 'required', 'password' => 'required', ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        Session::flash('flash_message', 'User updated!');


            if($user->roles->count()) {

                $user->roles()->detach($user->roles()->lists('role_id')->toArray());
            }

            $user->attachRoles($request->input('role_id'));

            $user->password=(bcrypt($user->password));
        
       $user->save();


        return redirect('admin/users');
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
        User::destroy($id);

        Session::flash('flash_message', 'User deleted!');

        return redirect('admin/users');
    }

    
}
