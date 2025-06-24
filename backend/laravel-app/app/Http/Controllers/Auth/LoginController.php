<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Debugging statement    
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;
        
        return response()->json(['token' => $token, 'role' => $user['type'], 'id' => $user['id']], 200);
    }
}
