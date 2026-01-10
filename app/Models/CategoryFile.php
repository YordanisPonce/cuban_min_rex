<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryFile extends Model
{
    protected $fillable = [
        'category_id',
        'file_id',
    ];
}
