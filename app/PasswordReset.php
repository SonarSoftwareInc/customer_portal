<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_reset_tokens';

    protected $fillable = [
        'token',
        'email',
        'contact_id',
        'account_id',
    ];
}
