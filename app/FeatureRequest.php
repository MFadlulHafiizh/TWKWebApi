<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeatureRequest extends Model
{
    //
    public $table = 'feature_request';

    protected $fillable = [
        'id_request', 'id_apps', 'priority', 'subject', 'detail', 'price', 'time_periodic', 'status'
    ];
    protected $primaryKey = 'id_request';
}
