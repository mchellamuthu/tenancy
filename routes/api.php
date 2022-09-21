<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix'=>'{tenant}','as'=>'tenant'],function($tenant){
    // Route::get('/',function($tenant){
    //     return $tenant;
    // });
    Route::get('/users',\App\Http\Controllers\Tenant\UserController::class);
});
