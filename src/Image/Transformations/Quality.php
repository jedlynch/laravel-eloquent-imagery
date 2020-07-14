<?php

namespace ZiffMedia\LaravelEloquentImagery\Image\Transformations;

use Illuminate\Support\Collection;
use Imagick;

class Quality implements ImagickTransformationInterface
{
    protected $automatic = false;

    protected $defaultQuality = .75;

    public function __construct()
    {
    }

    public function applyImagick(Collection $arguments, Imagick $imagick)
    {
        // if has command || automatic
        if (!$arguments->has('quality') && !$this->automatic) {
            return;
        }

        $quality = $arguments->get('quality') ?? $this->defaultQuality;

        $imagick->setCompressionQuality($quality);
        $imagick->setImageCompressionQuality($quality);
    }
}
