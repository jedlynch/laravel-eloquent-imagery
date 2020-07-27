<?php

namespace ZiffMedia\LaravelEloquentImagery\ImageTransformer;

use Illuminate\Support\Collection;
use RuntimeException;

class ImageTransformer
{
    public $transformations;

    protected $extension;

    public function __construct(Collection $transformations = null)
    {
        $this->transformations = $transformations ?? new Collection([
            new Transformations\JpegNormalize,
            new Transformations\JpegExif,
            new Transformations\Quality,
            new Transformations\Fit,
            new Transformations\Grayscale,
        ]);

        $extensions = (array) config('eloquent-imagery.render.transformations.extension_priority');

        foreach ($extensions as $extension) {
            if (extension_loaded($extension)) {
                $this->extension = $extension;
                break;
            }
        }

        if ($this->extension === null) {
            throw new RuntimeException('No valid image library was found in php, tried: ' . implode(', ', $extensions));
        }
    }

    public function transform(Collection $arguments, $imageBytes)
    {
        if ($this->extension === 'imagick') {

            // normalize background for imagick
            if ($arguments->has('background')) {
                $background = $arguments->get('background');

                if (preg_match('/^[A-Fa-f0-9]{3,6}$/', $background)) {
                    $arguments['background'] = '#' . $background;
                }
            }

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

        throw new RuntimeException('Currently only imagick is supported');
    }
}
