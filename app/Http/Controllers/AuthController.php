<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email'=>'required','email','unique',
            'name'=>'string',
            'password'=>'required'
        ]);
        $user = User::where('email',$request->email)->first();
        if($user){
            return response()->json([
                'message'=>'this email is already registered'
            ],400);
        }
        $user =  User::create([
            'email'=>$request->email,
            'name'=>$request->name,
            'password'=> Hash::make($request->password),
        ]);
        return response()->json([
            'data'=>$user,
            'message'=>'successfully registered'
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required','email','unique',
            'password'=>'required'
        ]);
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return response()->json([
                'message'=>'invalid Credentials'
            ],400);
        }
        $passwordCheck = Hash::check($request->password, $user->password);
        if(!$passwordCheck){
            return response()->json([
                'message'=>'invalid Credentials '
            ],400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data'=>$user,
            'token'=>$token,
            'message'=>'successfully logged in'
        ]);
    }
    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return response()->json([
            'message'=>'successfully logged out'
        ]);
    }
}
