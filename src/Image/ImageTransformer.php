<?php

namespace ZiffMedia\Laravel\EloquentImagery\Image;

use Illuminate\Support\Collection;

class ImageTransformer
{
    public $transformations;

    protected $extension;

    public static function createDefault()
    {
        // JpegColorspace
        // JpegStripExif
        // Crop
        // Trim
        // FitPad
        // FitLimit
        // FitResize
        // FitScale
        // Greyscale
        // Watermark

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
            $imagick->setSamplingFactors(['2x2', '1x1', '1x1']); // jpeg only?

            $this->transformations
                ->whereInstanceOf(Transformations\ImagickTransformationInterface::class)
                ->each(function ($transformation) use ($arguments, $imagick) {
                    $transformation->applyImagick($arguments, $imagick);
                });

            return $imagick->getImageBlob();
        }
    }

// - Reduce quality to 75%
// - If JPEG, set sampling factors?
// - what is this purpose?
//
// Standalone opt-in features:
// - Cropping X1,Y1,X2,Y2
// - Trim Tolerance
// - what is this?
// - Fitting (mutually exclusive):
// - FIT PAD LIMIT
// - using canvas of specific size, enlarge image to fit canvas, centered
// - FIT LIMIT
// - fit to given height/width by extending height or width (will skew?)
// - FIT SCALE
// - fit to given height/width, but keep aspect ratio?
// - FIT RESIZE
// - simply resize to height/width
// - Grayscale
// - Add watermark
}
