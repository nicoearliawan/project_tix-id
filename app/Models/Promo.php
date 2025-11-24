<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Promo extends Model
{
    //
    use softDeletes;

    protected $fillable = ['promo_code','discount','type','actived'];
}
