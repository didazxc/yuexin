<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\Controller;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function getUser(Request $request){
        return $this->response($request->user());
    }

    public function getUsers(Request $request){
        $users=User::with('projects')->get()->keyBy('id');
        return $this->response($users);
    }



}