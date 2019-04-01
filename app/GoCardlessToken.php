<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoCardlessToken extends Model
{
    protected $fillable = [
        'token',
        'account_id',
        'redirect_flow_id',
    ];
}
