<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword],
        ]);
    
        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );
    
        // Handle the response
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password successfully reset!'], 200);
        } else {
            return response()->json(['message' => 'Invalid token or email.'], 422);
        }
    }
    
}
