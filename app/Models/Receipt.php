<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model {
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'user_id',
        'item_id',
        'url',
        'path',
        'user_name',
        'item_title',
        'organization_name',
        'item_category',
        'item_date',
        'item_location',
        'growth_score',
    ];

}