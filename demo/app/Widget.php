<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ZiffMedia\Laravel\EloquentImagery\Eloquent\HasEloquentImagery;

class Widget extends Model
{
    use HasEloquentImagery;

    protected $eloquentImagery = [
        'image' => 'widgets/{id}.{extension}'
    ];
}
