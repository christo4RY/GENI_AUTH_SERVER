<?php

use App\Http\Controllers\Api\Auth\AuthenticatedController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Http;

//middleware('auth:api', 'scope:view-user')->
Route::middleware('guest:api')->group(function () {
    Route::post('/login', [AuthenticatedController::class,'store']);
    Route::post('/register', RegisterController::class);
    Route::post('forgot-password', ForgotPasswordController::class);
    Route::post('reset-password', ResetPasswordController::class);
});
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
                'project_logo'=>$request->project_logo,
                'project_url'=>$request->project_url,
                'project_dashboard_url'=>$request->project_dashboard_url,
            ]);
        }
    });
});
Route::middleware('auth:api')->group(function () {
    Route::post('change-password', [NewPasswordController::class,'update']);
    Route::get('/projects', function (Request $request) {
        $projects = Project::where('user_id', auth()->user()->id)->get();
        return response()->json($projects);
    });


    Route::post('/logout', [AuthenticatedController::class,'destroy']);
});
Route::post('/gotoproject/{user}', function (User $user, Request $request) {
    $project = Project::where('user_id', $user->id)->where('project_url', $request->project_url)->first();
    $res = Http::get($project->project_url."/auth/loginfromexternal", [
            "email" => $user->email,
        ]);
    return $project;
});
