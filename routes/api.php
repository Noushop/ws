<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;


Route::group([
  'prefix' => 'public'
], function () {
  Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
  ], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);
  });
});


Route::group(['middleware' => ['api', 'connect.client'], 'prefix' => 'private'], function () {
  Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'get'])->name('products.get');
  });

  Route::group(['prefix' => 'product'], function () {
    Route::get('/{product}', [ProductController::class, 'getOne'])->name('product.get_one');
    Route::post('create', [ProductController::class, 'create'])->name('product.create');
    Route::put('update/{product}', [ProductController::class, 'update'])->name('product.update');
  });

  Route::group(['prefix' => 'tickets'], function () {
    Route::get('/', [TicketController::class, 'get'])->name('tickets');
  });

  Route::group(['prefix' => 'ticket'], function () {
    Route::post('create', [TicketController::class, 'create'])->name('ticket.create');
    Route::get('/{ticket}', [TicketController::class, 'getOne'])->name('ticket');
  });

  Route::group(['prefix' => 'purchases'], function () {
    Route::get('', [PurchaseController::class, 'get'])->name('purchases');
  });

  Route::group(['prefix' => 'sale'], function () {
    Route::post('create', [SaleController::class, 'create'])->name('sale.create');
  });

  Route::group(['prefix' => 'admin', 'middleware' => 'check.admin.role'], function () {
    Route::group(['prefix' => 'user'], function () {
      Route::post('create', [UserController::class, 'create'])->name('create.user');
    });

    Route::group(['prefix' => 'users'], function () {
      Route::get('/', [UserController::class, 'list'])->name('list.users');
    });
  });
});

Route::get('migrate', function () {
  Artisan::queue('migrate');
});
