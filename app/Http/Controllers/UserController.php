<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|between:2,100',
      'email' => 'required|string|email|max:100|unique:users',
      'password' => 'required|string|min:6',
      'role' => 'required|in:lead,storer,cashier,accountant',
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
        ['password' => Hash::make($request->password)]
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

  public function list(Request $request)
  {
    $perPage = 12;

    $validator = Validator::make($request->all(), [
      'page' => 'integer|min:0'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 401);
    }

    return response()->json([
      'status' => true,
      'message' => '',
      'users' => User::orderBy('name', 'desc')->paginate($perPage),
    ], 200);
  }
}
