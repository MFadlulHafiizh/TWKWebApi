<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportBug extends Model
{
    public $table = 'report_bug';

    protected $fillable = [
        'id_apps', 'priority', 'subject', 'detail', 'status'
    ];
}
