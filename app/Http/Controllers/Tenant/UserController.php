<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenants\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __invoke(Request $request)
    {
        $users = User::count();

        return response()->json(['users'=>$users]);
    }

}
