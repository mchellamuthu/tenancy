<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant;
use App\Models\Tenants\User;
use Illuminate\Http\Request;
use App\Jobs\Tenants\AddUsersJob;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __invoke(Request $request)
    {
        $tenant_name = $request->route()->originalParameter('tenant');
        AddUsersJob::dispatch()->onQueue($tenant_name);
        $users = User::count();
        return response()->json(['users' => $users]);
    }
}
