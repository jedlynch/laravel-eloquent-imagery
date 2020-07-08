<?php

namespace ZiffMedia\Laravel\EloquentImagery\Controller\RequestStrategies;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ZiffMedia\Laravel\EloquentImagery\Controller\ImageRequest;

class LegacyStrategy
{
    protected $urlModifierRegexes = [
        'width'      => '/^size_(?P<value>\d*){0,1}x(?:\d*){0,1}$/', // set width
        'height'     => '/^size_(?:\d*){0,1}x(?P<value>\d*){0,1}$/', // set height
        'fit'        => '/^fit_(?P<value>[a-z]+)$/', // set height
        'grayscale'  => '/^grayscale$/', // grayscale
        'quality'    => '/^quality_(?P<value>[0-9]+)/', //quality, if applicable
        'background' => '/^bg_(?P<value>[\da-f]{6})$/', // background hex
        'trim'       => '/^trim_(?P<value>\d+)$/', // trim, tolerance
        'crop'       => '/^crop_(?P<value>[\d,?]+)$/' // crop operations
    ];

    public function execute(Request $request, ImageRequest $imageRequest)
    {
        $path = $request->route('path');

        $pathInfo = pathinfo($path);
        $imagePath = $pathInfo['dirname'] . '/';

        $filenameWithoutExtension = $pathInfo['filename'];

        if (strpos($filenameWithoutExtension, '.') !== false) {
            $filenameParts = explode('.', $filenameWithoutExtension);
            $filenameWithoutExtension = $filenameParts[0];
            $imagePath .= "{$filenameWithoutExtension}.{$pathInfo['extension']}";

            $modifierSpecs = array_slice($filenameParts, 1);

            foreach ($modifierSpecs as $modifierSpec) {
                $matches = [];
                foreach ($this->urlModifierRegexes as $modifier => $regex) {
                    if (preg_match($regex, $modifierSpec, $matches)) {
                        $imageRequest->imageModifiers[$modifier] = $matches['value'] ?? true;
                    }
                }
            }
        } else {
            $imagePath .= $pathInfo['basename'];
        }

        $imageRequest->imagePath = $imagePath;

        if (isset($imageRequest->imageModifiers['background'])) {
            $imageRequest->imageModifiers['background'] = '#' . $imageRequest->imageModifiers['background'];
        }
    }
}

