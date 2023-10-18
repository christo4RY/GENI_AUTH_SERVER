<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        try {

            $user = User::create([
                'fullname'=>$request->firstname." ".$request->lastname,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);

            UserDetail::create([
                'user_id'=>$user->id,
                'firstname'=>$request->firstname,
                'lastname'=>$request->lastname,
                'avatar'=>$request->avatar,
                'phone'=>$request->phone,
                'country'=>$request->country,
                'city'=>$request->city,
                'address'=>$request->address,
            ]);

            $token = $user->createToken('GENI')->accessToken;
        } catch (\Throwable $th) {
            $th->getMessage();
        }

        return response()->json([
            'data'=>[
                'id'=>$user->id,
                'token'=>$token,
                'email'=>$user->email,
                'username'=>$user->fullname,
                'message'=>'user '.$user->fullname.' was created',
            ]
        ]);
    }
}
