<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Purchase;
use Carbon\Carbon;

class PurchaseController extends Controller
{
  public function get(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'dateStart' => 'date',
      'dateEnd' => 'date',
      'page' => 'integer|min:0'
    ]);

    $perPage = 30;

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 400);
    }

    if ($request->dateStart && $request->dateEnd && $request->dateStart !== $request->dateEnd) {
      $startDate = Carbon::createFromFormat('Y-m-d', $request->dateStart);
      $endDate = Carbon::createFromFormat('Y-m-d', $request->dateEnd);

      return response()->json([
        'status' => true,
        'message' => '',
        'purchases' => Purchase::whereBetween('created_at', [$startDate, $endDate])
          ->orWhereDate('created_at', $request->dateStart)
          ->orWhereDate('created_at', $request->dateEnd)
          ->with('product')
          ->with('user')
          ->orderBy('created_at', 'desc')
          ->paginate($perPage),
      ], 200);
    }

    if ($request->dateStart) {
      return response()->json([
        'status' => true,
        'message' => 'start Date Only',
        'purchases' => Purchase::whereDate('created_at', $request->dateStart)
          ->with('product')
          ->with('user')
          ->orderBy('created_at', 'desc')
          ->paginate($perPage),
      ], 200);
    }

    if ($request->dateEnd) {
      return response()->json([
        'status' => true,
        'message' => '',
        'purchases' => Purchase::whereDate('created_at', $request->dateEnd)
          ->with('product')
          ->with('user')
          ->orderBy('created_at', 'desc')
          ->paginate($perPage),
      ], 200);
    }

    return response()->json([
      'status' => true,
      'message' => '',
      'purchases' => Purchase::with('product')
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->paginate($perPage),
    ], 200);
  }
}
