<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        // dd($request->all());
        try {
            $user = User::create([
                'fullname'=>$request->firstname." ".$request->lastname,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);

            $detail = UserDetail::create([
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


        // event(new Registered($user));

        Auth::login($user);

        return response()->json([
            'data'=>[
                'id'=>$user->id,
                'token'=>$token,
                'message'=>'user '.$user->fullname.' was created',
            ]
        ]);
        // return redirect(RouteServiceProvider::HOME);
    }
}
