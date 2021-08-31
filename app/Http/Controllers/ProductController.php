<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Purchase;
use GuzzleHttp\Handler\Proxy;

class ProductController extends Controller
{
  public function get(Request $request)
  {
    if ($request->search) {
      $products = Product::where('name', 'LIKE', "%$request->search%")
        ->orwhere('barcode', 'LIKE', "%$request->search%")
        ->paginate(6);
    } else {
      $products = Product::paginate(6);
    }

    return response()->json($products, 200);
  }

  public function create(Request $request)
  {
    $validator = Validator::make($request->only(['barcode', 'name', 'price', 'cost', 'quantity']), [
      'barcode' => 'required|string|between:9,20|unique:products',
      'name' => 'required|string|max:50',
      'price' => 'required|integer|min:0',
      'cost' => 'required|integer|min:0',
      'quantity' => 'required|integer|min:0',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors()->first(),
      ], 400);
    }

    $product = Product::create($validator->validated());

    $purchased = $this->purchase($request->barcode, $request->quantity, $request->cost);

    if (!$purchased) {
      return response()->json([
        'status' => true,
        'message' => 'Product are successfully updated but not added to purchase list due to an error',
      ], 409);
    }

    return response()->json([
      'status' => true,
      'message' => 'Product successfully added',
      'product' => $product
    ], 201);
  }

  public function update(Request $request, Product $product)
  {

    try {
      $validator = Validator::make($request->only(['barcode', 'name', 'price', 'cost', 'quantity']), [
        'barcode' => 'string|between:9,20|unique:products,barcode',
        'name' => 'string|max:50',
        'price' => 'integer|min:0',
        'quantity' => 'integer|min:0',
        'cost' => 'integer|min:0|required_unless:quantity,null,0',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status' => false,
          'message' => $validator->errors()->first(),
        ], 400);
      }
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => 'Internal error while validating request',
        'error' => $th->getMessage(),
      ], 500);
    }

    try {
      if ($request->has('barcode')) {
        $product->barcode = $request->barcode;
      }

      if ($request->has('name')) {
        $product->name = $request->name;
      }

      if ($request->has('price')) {
        $product->price = $request->price;
      }

      if ($request->has('cost')) {
        $product->cost = $request->cost;
      }

      // TODO: add track on barcode update

      if ($request->has('quantity') && $request->quantity > 0) {
        $product->quantity = $product->quantity + $request->quantity;

        $purchased = $this->purchase($product->barcode, $request->quantity, $request->cost);

        if (!$purchased) {
          return response()->json([
            'status' => true,
            'message' => 'Product are not added to purchase list due to an error',
          ], 409);
        }
      }

      $product->save();
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => 'Internal error while using the database',
        'error' => $th->getMessage(),
      ], 500);
    }

    return response()->json([
      'status' => true,
      'message' => 'Product successfully updated',
    ], 201);
  }

  private function purchase($barcode, $quantity, $cost)
  {
    $purchase = new Purchase;

    $purchase->product_barcode = $barcode;
    $purchase->cost = $cost;
    $purchase->quantity = $quantity;
    $purchase->user_id = auth()->user()->id;

    return $purchase->save();
  }

  public function getOne(Product $product)
  {
    return response()->json([
      'status' => true,
      'message' => '',
      'product' => $product,
    ], 200);
  }
}
