<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string encode
 * @property mixed message
 * @property float time
 * @property mixed type
 * @property string key
 * @property bool|string iv
 * @property string message_type
 * @property false|string path
 */
class Message extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
