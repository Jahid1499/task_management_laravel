<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function Index()
    {
        return response()->json([
            'users' => User::all(),
        ], 200);
    }
    public function Login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user == null)
        {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->password != $request->input('password'))
        {
            return response()->json(['error' => 'Incorrect password'], 401);
        }

        $token = JWTToken::CreateToken($user->email, $user->id);
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);

    }

    public function Registration(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::get();
        $role = "user";
        if ($user->count() == 0)
        {
            $role = "admin";
        }
        try {
            $newUser = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role' => $role,
                'status' => "active",
            ]);

            $user = User::findOrFail($newUser->id);
            $token = JWTToken::CreateToken($user->email, $user->id);
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'errors' => "Failed to registration",
            ], 422);
        }
    }

    public function Logout(Request $request)
    {


    }
}
