<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware'=>'tenant.enforce','prefix'=>'{tenant}'],function($tenant){
    // Route::get('/',function($tenant){
    //     return $tenant;
    // });
    Route::get('/users',\App\Http\Controllers\Tenant\UserController::class);
});
