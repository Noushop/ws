<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_barcode',
    'prices',
    'quantity',
    'ticket_id',
  ];

  public function ticket()
  {
    return $this->belongsTo(Ticket::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_barcode', 'barcode');
  }
}
