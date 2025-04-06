<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', function () {
    
    $currentTime = Carbon::now();
    // $expirationTime = $currentTime->addHours(2);
    $expirationTime = null;

    $credentials = [
        'email' => 'admin3@admin.com',
        'password' => 'password3'
    ];

    $user = new \App\Models\User();

    $user->name = 'Admin3';
    $user->email = $credentials['email'];
    $user->password = Hash::make($credentials['password']);

    $user->save();

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        //false positive error
        $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete'], $expirationTime);
        $updateToken = $user->createToken('update-token', ['create', 'update'], $expirationTime);
        $basicToken = $user->createToken('basic-token', ['none'], $expirationTime);
        return [
            'admin' => $adminToken->plainTextToken,
            'update' => $updateToken->plainTextToken,
            'basic' => $basicToken->plainTextToken,
        ];
    }


Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.request');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::post('/stripe/webhook', 'StripeWebhookController@handleWebhook');


});
