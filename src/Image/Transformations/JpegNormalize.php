<?php

namespace ZiffMedia\Laravel\EloquentImagery\Image\Transformations;

use Illuminate\Support\Collection;
use Imagick;

class JpegNormalize implements ImagickTransformationInterface
{
    protected $fixColorspace = true;

    public function applyImagick(Collection $arguments, Imagick $imagick)
    {
        if ($this->fixColorspace) {
            if ($imagick->getColorspace() === Imagick::COLORSPACE_CMYK) {
                $imagick->setColorspace(Imagick::COLORSPACE_RGB);
            }
        }
    }
}

