<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalTemporaryToken extends Model
{
    protected $fillable = [ 'token', 'account_id' ];
}
