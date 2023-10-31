<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AuthenticateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedController extends Controller
{

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $user = auth()->user();
        $token = $user->createToken('GENI')->accessToken;
        return response()->json([
            'data'=>[
                'id'=>$user->id,
                'token'=>$token,
                'email'=>$user->email,
                'username'=>$user->fullname,
                'message'=>'login successful.',
            ]
        ]);

    }

    public function getUserProfile(Request $request)
    {
        return response()->json(['userProfile'=> User::findOrFail($request->user()->id)->load('userdetail')]);
    }

    public function update(AuthenticateRequest $request)
    {
        // dd($request->file('avatar'));
        $request->user()->update([
            'fullname'=>$request->firstname." ".$request->lastname,
            'email'=>$request->email
        ]);

        UserDetail::where('user_id', $request->user()->id)->first()->update([
                'firstname'=>$request->firstname,
                'lastname'=>$request->lastname,
            'phone'=>$request->phone,
            'country'=>$request->country,
            'city'=>$request->city,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Profile updated',
        ]);
    }

    public function destroy(Request $request)
    {
        $result = $request->user()->token()->revoke();
        if($result) {
            $response = response()->json(['error'=>false,'msg'=>'User logout successfully.','result'=>[]], 200);
        } else {
            $response = response()->json(['error'=>true,'msg'=>'Something is wrong.','result'=>[]], 400);
        }
        return $response;
    }
}
