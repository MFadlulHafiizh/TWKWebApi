<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $table = 'ticket';

    protected $fillable = [
        'id_ticket', 'id_apps', 'type', 'priority', 'subject', 'detail', 'price', 'time_periodic', 'status', 'aproval_stat'
    ];

    protected $primaryKey = 'id_ticket';
}
