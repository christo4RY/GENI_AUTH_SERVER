<?php

use App\Http\Controllers\ProfileController;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Passport\Client;
use Tzsk\Otp\Facades\Otp;
use App\Models\Otp as OtpModel;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/check/oauth', function (Request $request) {
    if(Auth::check($request->user())) {
        $oauth_redirect_uri =$request->oauth_redirect_uri;
        $redirect_uri =  $request->redirect_uri;
        if(!Client::where('user_id', $request->user()->id)->get()->count()) {
            Artisan::call('passport:client --user_id='.$request->user()->id.' --name='.str_replace(' ', '_', $request->user()->fullname).' --redirect_uri='.$oauth_redirect_uri);
        }
        $client = Client::where('user_id', $request->user()->id)->latest()->first();

        $query = http_build_query([
            'provider'=>'GENI',
            'client_id'=>$client->id,
            'client_secret'=>$client->secret,
        ]);
        return redirect($redirect_uri."?".$query);

    } else {
        $request->session()->put('redirect_login_uri', $request->get('redirect_login_uri'));
        return redirect(route('email.verify'));
    }
})->name('check.oauth');


Route::get('/verify-otp', function () {
    return Inertia::render('Auth/Otp');
})->name('verify.otp');

Route::get('/test', function () {
    return redirect()->away('http://127.0.0.1:8181');
});

Route::post('/verify-otp', function (Request $request) {
    $otp = OtpModel::where('otp', $request->otp)->first();
    if(!$otp) {
        return response()->json([
            'status'=>404,
            'msg'=>'OTP wrong!'
        ]);
    }

    if(Otp::check($otp->otp, 123456)) {
        auth()->login($otp->user);
        $login = $request->session()->pull('redirect_login_uri');
        return response()->json([
            'status'=>200,
            'redirect_url'=>$login,
            'msg'=>"Validated Successful"
        ]);
    } else {
        return response()->json([
            'status'=>404,
            'msg'=>'OTP was invalid'
        ]);
    }
})->name('verify.otp');

Route::get('/email/verify', function () {
    return Inertia::render('Auth/Email/Verify');
})->name('email.verify');
Route::post('/email/verify', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if(!$user) {
        return response()->json([
            'status'=>404,
            'msg'=>'email not exist!'
        ]);
    }

    auth()->login($user);
    $login = $request->session()->pull('redirect_login_uri');
    return response()->json([
        'status'=>200,
        'redirect_url'=>$login,
        'msg'=>"Validated Successful"
    ]);
    //otp

    $otp = Otp::generate(123456);
    OtpModel::create([
        'user_id'=>$user->id,
        'otp'=>$otp
    ]);
    Mail::to($request->email)->queue(new OtpMail($otp));
    return response()->json([
        'status'=>200,
        'msg'=>"Send OTP to your email account"
    ]);
})->name('email.verify');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
