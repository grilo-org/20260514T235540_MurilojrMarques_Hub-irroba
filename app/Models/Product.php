<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'description', 'price', 'stock', 'images', 'tags'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'images' => 'array',
        'tags' => 'array'
    ];
}
