<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string encode
 * @property mixed message
 */
class Message extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
