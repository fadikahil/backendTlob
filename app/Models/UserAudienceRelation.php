<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAudienceRelation extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_email',
        'organization_id',
        'type',
    ];

}