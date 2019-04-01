<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreationToken extends Model
{
    protected $fillable = [
        'token',
        'email',
        'account_id',
        'contact_id',
    ];
}
