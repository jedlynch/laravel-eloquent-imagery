<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ZiffMedia\LaravelEloquentImagery\Eloquent\HasEloquentImagery;

class SingleImageExample extends Model
{
    use HasEloquentImagery;

    protected $casts = [
        'variations' => 'json'
    ];

    protected $eloquentImagery = [
        'image' => 'single-image-examples/{id}.{extension}'
    ];
}
