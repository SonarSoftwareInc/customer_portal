<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsernameLanguage extends Model
{
    protected $fillable = [
        'username',
        'language',
    ];
}
