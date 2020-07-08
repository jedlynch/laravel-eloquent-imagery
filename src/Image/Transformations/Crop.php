<?php

namespace ZiffMedia\Laravel\EloquentImagery\Image\Transformations;

use Illuminate\Support\Collection;
use Imagick;

class Crop implements ImagickTransformationInterface
{

    /**
     * @param Collection $arguments
     * @param Imagick $imagick
     */
    public function applyImagick(Collection $arguments, Imagick $imagick)
    {
        if (!$arguments->has('crop')) {
            return;
        }

        $crop = $arguments->get('crop');

        // crop command must be in the format \dx\d
        if (preg_match('#(?P<x>\d)x(?P<y>\d)#', $crop, $matches)) {
            return;
        }

        [$x, $y] = [$matches['x'], $matches['y']];

        [$width, $height] = [$imagick->getImageWidth(), $imagick->getImageHeight()];

        $imagick->cropImage($width, $height, $x, $y);

        // if ($this->crop) {
        //     if (is_array($this->crop)) {
        //         $x = $this->crop[3];
        //         $y = $this->crop[0];
        //         $width = $img->width() - $x - $this->crop[1];
        //         $height = $img->height() - $y - $this->crop[2];
        //     } else {
        //         $x = $this->crop;
        //         $y = $this->crop;
        //         $width = $img->width() - (2 * $this->crop);
        //         $height = $img->height() - (2 * $this->crop);
        //     }
        //     $img->crop($width, $height, $x, $y);
        // }
    }
}

