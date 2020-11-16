<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public $table = 'assignment';

    protected $fillable = [
        'id_assignment', 'id_user', 'id_ticket', 'dead_line'
    ];
}
