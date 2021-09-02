<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use Carbon\Carbon;

class TicketController extends Controller
{
  public function create(Request $request)
  {
    $validator = Validator::make($request->only(['prices']), [
      'prices' => 'min:0|integer'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 400);
    }

    $ticket = Ticket::create([
      'prices' => $request->input('prices', 0),
      'user_id' => auth()->user()->id,
    ])->id;

    if (!$ticket) {
      return response()->json([
        'status' => false,
        'message' => 'Can\'t create a ticket',
      ], 403);
    }

    return response()->json([
      'status' => true,
      'message' => 'Ticket created',
      'ticket' => $ticket,
    ], 201);
  }

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
        'ticket' => Ticket::with('user')
          ->orderBy('created_at', 'desc')
          ->whereBetween('created_at', [$startDate, $endDate])
          ->orWhereDate('created_at', $request->dateStart)
          ->orWhereDate('created_at', $request->dateEnd)
          ->paginate($perPage),
      ], 200);
    }

    if ($request->dateStart) {
      return response()->json([
        'status' => true,
        'message' => 'start Date Only',
        'ticket' => Ticket::with('user')->orderBy('created_at', 'desc')->whereDate('created_at', $request->dateStart)->paginate($perPage),
      ], 200);
    }

    if ($request->dateEnd) {
      return response()->json([
        'status' => true,
        'message' => '',
        'ticket' => Ticket::with('user')->orderBy('created_at', 'desc')->whereDate('created_at', $request->dateEnd)->paginate($perPage),
      ], 200);
    }

    return response()->json([
      'status' => true,
      'message' => '',
      'ticket' => Ticket::with('user')->orderBy('created_at', 'desc')->paginate($perPage),
    ], 200);
  }

  public function getOne(Ticket $ticket)
  {
    return response()->json([
      'status' => true,
      'message' => '',
      'ticket' => $ticket->load('user')->load('sales.product'),
    ], 200);
  }
}
