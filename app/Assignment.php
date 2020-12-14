<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public $table = 'assignment';

    const CREATED_AT = 'assign_at';
    const UPDATED_AT = 'assign_updated_at';
    protected $fillable = [
        'id_assignment', 'id_user', 'id_ticket', 'dead_line', 
    ];

}
