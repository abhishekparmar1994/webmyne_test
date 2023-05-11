<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    protected $fillable = ['title', 'description'];

    public function category(){
        return $this->belongsTo(Products::class, 'id', 'category_id');
    }
}
