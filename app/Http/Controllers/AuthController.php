<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register', 'baby']]);
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

  public function baby(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|between:2,100',
      'email' => 'required|string|email|max:100|unique:users',
    ]);

    if ($validator->fails()) {
      return response()->json(array(
        "status" => false,
        "message" => $validator->errors()->first()
      ), 400);
    }

    try {
      $user = User::create(array_merge(
        $validator->validated(),
        [
          'password' => Hash::make(env('SUPER_ADMIN_PASSWORD') || 'password'),
          'role' => 'super-admin',
        ]
      ));

      if (!$user) {
        throw new \Throwable('User not created');
      }
    } catch (\Throwable $th) {
      return response()->json(array(
        "status" => false,
        "message" => $th->getMessage(),
      ), 400);
    }

    return response()->json([
      'status' => true,
      'message' => 'User successfully registered',
      'user' => $user
    ], 201);
  }

  public function logout()
  {
    auth()->logout();

    return response()->json([
      'status' => true,
      'message' => 'User successfully signed out'
    ], 200);
  }


  public function userProfile()
  {
    return response()->json([
      'status' => true,
      'message' => 'User information',
      'user' => auth()->user(),
    ], 200);
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

// public function refresh()
// {
//   return response()->json([
//     'status' => true,
//     'message' => 'Token refreshed',
//     'data' => $this->createNewToken(auth()->refresh()),
//   ] . 201);
// }
