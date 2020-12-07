<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationTable extends Model
{
    public $table = 'notification';
    protected $fillable = [
        'id_notif', 'id_user', 'id_ticket', 'from', 'title', 'message', 'read_at'
    ];
    protected $primaryKey = 'id_notif';
}
