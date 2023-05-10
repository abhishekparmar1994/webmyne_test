<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Products extends Model
{
    use HasFactory;
    protected $table = 'product';
    
    protected $fillable = ['category_id', 'title','image','description','price','currency','in_stock','status'];

    public function category(){
        return $this->hasMany(Category::class, 'category_id', 'id');
    }
}
