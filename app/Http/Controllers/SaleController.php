<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Ticket;

class SaleController extends Controller
{
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'pan.*.barcode' => 'required|string|between:9,20|exists:products',
      'pan.*.prices' => 'required|integer|min:0',
      'pan.*.quantity' => 'required|integer|min:0',
      'ticket' => 'required|integer|min:0|exists:tickets,id',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 401);
    }

    DB::beginTransaction();

    try {
      foreach ($request->pan as $pan) {
        $sale = Sale::create([
          'product_barcode' => $pan['barcode'],
          'prices' => $pan['prices'],
          'quantity' => $pan['quantity'],
          'ticket_id' => $request->ticket,
        ]);

        if (!$sale) {
          DB::rollBack();
          return response()->json([
            'status' => false,
            'message' => 'Can\'t create a save',
          ], 403);
        }

        $product = Product::where('barcode', $pan['barcode'])->first();

        if ($product->quantity < $pan['quantity']) {
          DB::rollBack();
          return response()->json([
            'status' => false,
            'message' => 'Not enougth quantity for ' . $pan['barcode'],
          ], 403);
        }

        $product->decrement('quantity', $pan['quantity']);

        if (!$product) {
          DB::rollBack();
          return response()->json([
            'status' => false,
            'message' => 'Can\'t decrement the product it',
          ], 403);
        }

        $ticket = Ticket::where('id', $request->ticket)->increment('prices', $pan['prices']);

        if (!$ticket) {
          DB::rollBack();
          return response()->json([
            'status' => false,
            'message' => 'Can\'t update the ticket',
          ], 403);
        }
      }

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json([
        'status' => false,
        'message' => 'Error occured in the query',
      ], 403);
    }

    return response()->json([
      'status' => true,
      'message' => 'Saved'
    ], 200);
  }
}
