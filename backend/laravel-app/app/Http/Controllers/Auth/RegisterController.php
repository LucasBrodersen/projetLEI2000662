<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\StrongPassword;

class RegisterController extends Controller
{
    public function register(Request $request)
    {   
        info("passou aqui register");
        info($request);

        // Validate request data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', 'string', 'confirmed', new StrongPassword],
            'type' => 'required|string', // Ensure type is not nullable
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'enrolment_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended,pending', // Validate against the possible enum values
        ]);

        info('request pós validate');
        info($request);
        // Create new user with validated data
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,           // Save 'type'
            'address' => $request->address,     // Save 'address'
            'city' => $request->city,           // Save 'city'
            'state' => $request->state,         // Save 'state'
            'postal_code' => $request->postal_code, // Save 'postal_code'
            'enrolment_date' => $request->enrolment_date, // Save 'postal_code'
            'status' => $request->status ?? 'active', // Save 'postal_code'
        ]);

        // Save the user to the database
        $user->save();

        info($user);

        // Return a success response
        return response()->json(['message' => 'User registered successfully'], 201);
    }
}
