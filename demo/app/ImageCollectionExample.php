<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ZiffMedia\Laravel\EloquentImagery\Eloquent\HasEloquentImagery;

class ImageCollectionExample extends Model
{
    use HasEloquentImagery;

    protected $eloquentImagery = [
        'images' => [
            'path' => 'image-collection-examples/{id}/{index}.{extension}',
            'collection' => true
        ]
    ];
}
