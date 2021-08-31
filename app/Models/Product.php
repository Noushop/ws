<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;

  protected $fillable = [
    'barcode',
    'name',
    'price',
    'cost',
    'quantity',
  ];

  protected $primaryKey = 'barcode';

  public $incrementing = false;

  protected $keyType = 'string';

  public function purchase()
  {
    return $this->belongsTo(Purchase::class);
  }
}
