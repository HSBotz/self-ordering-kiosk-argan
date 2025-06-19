<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'icon',
        'is_active',
        'has_variants'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_variants' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
