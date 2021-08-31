<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
  }

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 400);
    }

    if (!$token = auth()->attempt($validator->validated())) {
      return response()->json([
        'status' => false,
        'message' => 'Unauthorized credentials',
      ], 401);
    }

    return response()->json([
      'status' => true,
      'message' => 'Successfully logged in',
      'data' => $this->createNewToken($token)->original,
    ], 200);
  }

  public function logout()
  {
    auth()->logout();

    return response()->json([
      'status' => true,
      'message' => 'User successfully signed out'
    ], 200);
  }

  public function refresh()
  {
    return response()->json([
      'status' => true,
      'message' => 'Token refreshed',
      'data' => $this->createNewToken(auth()->refresh()),
    ] . 201);
  }

  public function userProfile()
  {
    return response()->json([
      'status' => true,
      'message' => 'User information',
      'user' => auth()->user(),
    ]);
  }

  protected function createNewToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60,
      'user' => auth()->user(),
    ], 201);
  }
}
