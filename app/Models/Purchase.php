<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_barcode',
    'cost',
    'quantity',
    'user_id',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_barcode', 'barcode');
  }
}
