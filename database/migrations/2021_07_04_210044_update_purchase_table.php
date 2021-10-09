<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('purchases', function (Blueprint $table) {
      $table->dropForeign('purchases_product_barcode_foreign');
      $table->dropForeign('purchases_user_id_foreign');
      // $table->foreign('product_barcode')->references('barcode')->on('products')->onUpdate('cascade')->onDelete('cascade');
      // $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
}
