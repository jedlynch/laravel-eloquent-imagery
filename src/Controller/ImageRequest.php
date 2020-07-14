<?php

namespace ZiffMedia\LaravelEloquentImagery\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ImageRequest
{
    /** @var string */
    public $imagePath = '';

    /** @var Collection */
    public $imageModifiers;

    public static function create(Request $request): self
    {
        $imageRequest = new static;

        (new RequestStrategies\LegacyStrategy())->execute($request, $imageRequest);

        return $imageRequest;
    }

    public function __construct()
    {
        $this->imageModifiers = new Collection();
    }

    public function isPlaceholderRequest()
    {
        return false;
    }
}
