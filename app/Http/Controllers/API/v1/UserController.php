<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Project;

class UserController extends Controller
{
    public function getUser(Request $request){
        return $request->user();
    }

    public function getUsers(Request $request){
        return User::with('projects')->get()->keyBy('id');
    }



}