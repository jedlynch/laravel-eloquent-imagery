<?php

namespace ZiffMedia\Laravel\EloquentImagery\Image\Transformations;

use Illuminate\Support\Collection;
use Imagick;

class Grayscale implements ImagickTransformationInterface
{
    public function applyImagick(Collection $arguments, Imagick $imagick)
    {
        if (!$arguments->has('grayscale')) {
            return;
        }

        // $imagick->setColorspace(Imagick::COLORSPACE_GRAY);
        $imagick->setImageColorspace(Imagick::COLORSPACE_GRAY);
    }
}

