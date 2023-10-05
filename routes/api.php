<?php

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

//middleware('auth:api', 'scope:view-user')->

Route::middleware('auth:api', 'scope:view-user')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('userdetail');
    });


    Route::post('/project', function (Request $request) {
        $user = User::where('email', $request->user_email)->first();
        $exist = Project::where('user_id', $user->id)->first();
        if(!$exist) {
            Project::create([
                'user_id'=>$user->id,
                'project_name'=>$request->project_name,
                'project_url'=>$request->project_url,
            ]);
        }
    });
});
Route::middleware('auth:api')->group(function () {
    Route::get('/projects', function (Request $request) {
        $projects = Project::where('user_id', auth()->user()->id)->get();
        return response()->json(['projects'=>$projects]);
    });
});
