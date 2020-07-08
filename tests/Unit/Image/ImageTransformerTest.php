<?php

namespace ZiffMedia\Laravel\EloquentImagery\Test\Unit\Image;

use Illuminate\Support\Collection;
use ZiffMedia\Laravel\EloquentImagery\Image\ImageTransformer;
use ZiffMedia\Laravel\EloquentImagery\Test\Unit\AbstractTestCase;

class ImageTransformerTest extends AbstractTestCase
{
    public function testImageTransformerHasTransformations()
    {
        $imageTransformer = ImageTransformer::createDefault();

        $this->assertInstanceOf(Collection::class, $imageTransformer->transformations);
        $this->assertCount(1, $imageTransformer->transformations);
    }

    public function testImageTransformerSetsQuality()
    {
        $imageTransformer = ImageTransformer::createDefault();

        $bytesOriginal = file_get_contents(__DIR__ . '/picture.jpg');

        $newBytes = $imageTransformer->transform(collect(['quality' => 50]), $bytesOriginal);

        $this->assertLessThan(strlen($bytesOriginal), strlen($newBytes));
    }
}

