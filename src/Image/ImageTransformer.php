<?php

namespace ZiffMedia\LaravelEloquentImagery\Image;

use Illuminate\Support\Collection;

class ImageTransformer
{
    public $transformations;

    protected $extension;

    public static function createDefault()
    {
        return new ImageTransformer(new Collection([
            new Transformations\JpegNormalize,
            new Transformations\JpegExif,
            new Transformations\Quality,
            new Transformations\Fit,
            new Transformations\Grayscale,
        ]));
    }

    public function __construct(Collection $transformations = null)
    {
        $this->transformations = $transformations ?? new Collection;

        $extensions = (array) config('eloquent-imagery.render.transformations.extension_priority');

        foreach ($extensions as $extension) {
            if (extension_loaded($extension)) {
                $this->extension = $extension;
                break;
            }
        }

        if ($this->extension === null) {
            throw new \RuntimeException('No valid image library was found in php, tried: ' . implode(', ', $extensions));
        }
    }

    public function transform(Collection $arguments, $imageBytes)
    {
        if ($this->extension === 'imagick') {
            $imagick = new \Imagick();
            $imagick->readImageBlob($imageBytes);

            $isCoalesced = false;

            if ($imagick->getImageFormat() === 'GIF' && $imagick->getNumberImages() > 1) {
                $imagick = $imagick->coalesceImages();
                $isCoalesced = true;
            }

            $this->transformations
                ->whereInstanceOf(Transformations\ImagickTransformationInterface::class)
                ->each(function ($transformation) use ($arguments, $imagick) {
                    $transformation->applyImagick($arguments, $imagick);
                });

            if ($isCoalesced) {
                $imagick = $imagick->deconstructImages();
            }

            return $imagick->getImagesBlob();
        }
    }
}
