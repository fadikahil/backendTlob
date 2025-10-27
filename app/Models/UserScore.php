<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserScore extends Model {

    use HasFactory, SoftDeletes;


    protected $fillable = [
        'id',
        'user_id',
        'type',
        'score',
    ];

    /**
     * Get the user being reviewed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}