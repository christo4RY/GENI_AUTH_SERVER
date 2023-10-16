<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) use ($request) {
            return env('FRONTEND_URL').'/reset-password?token='.$token.'&email='.$request->email;
        });
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'email'=>$request->email,
                'msg'=> 'Check your email box'
            ]);
        }

    }
}
