<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\UserRequest;
use App\Services\BackEnd\UserService;

class UserController extends Controller
{

    public function user()
    {
        $users = UserService::getUser();
        return view('backend.users.user', compact('users'));
    }

    public function saveUser(UserRequest $request)
    {

        $user = UserService::saveUser($request->all());
        if ($user === true) {
            return redirect()->route('user')->with('success', 'Save User Successfull.');
        } else {
            return redirect()->route('user')->with('error', $user);
        }
    }

    public function saveEditUser(Request $request)
    {
        $editUser = UserService::saveEditUser($request->all());
        if ($editUser === true) {
            return response()->json(['success' => true, 'status' => "Update User Successfull."]);
        } elseif ($editUser == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $editUser]);
        }
    }

    public function inactiveUser($id = null)
    {
        $inactiveUser = UserService::inactiveUser($id);
        if ($inactiveUser === true) {
            return response()->json(['success' => true, 'status' => "User Inactive Successfull."]);
        } elseif ($inactiveUser == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveUser]);
        }
    }

    public function activeUser($id = null)
    {
        $activeUser = UserService::activeUser($id);
        if ($activeUser === true) {
            return response()->json(['success' => true, 'status' => "User Active Successfull."]);
        } elseif ($activeUser == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeUser]);
        }
    }

//=========== Admin Section ==============

    public function admin()
    {
        $admins = UserService::getAdmin();
        return view('backend.admins.admin', compact('admins'));
    }

    public function saveAdmin(Request $request)
    {
        //return $request->all();

        $admin = UserService::saveAdmin($request->all());
        if ($admin === true) {
            return redirect()->route('admin')->with('success', 'Save Admin Successfull.');
        } else {
            return redirect()->route('admin')->with('error', $admin);
        }
    }

    public function saveEditAdmin(Request $request)
    {
        $editAdmin = UserService::saveEditAdmin($request->all());
        if ($editAdmin === true) {
            return response()->json(['success' => true, 'status' => "Update Admin Successfull."]);
        } elseif ($editAdmin == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $editAdmin]);
        }
    }

    public function inactiveAdmin($id = null)
    {
        $inactiveAdmin = UserService::inactiveAdmin($id);
        if ($inactiveAdmin === true) {
            return response()->json(['success' => true, 'status' => "Category Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveAdmin]);
        }
    }

    public function activeAdmin($id = null)
    {
        $activeAdmin = UserService::activeAdmin($id);
        if ($activeAdmin === true) {
            return response()->json(['success' => true, 'status' => "Category Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeAdmin]);
        }
    }


}
