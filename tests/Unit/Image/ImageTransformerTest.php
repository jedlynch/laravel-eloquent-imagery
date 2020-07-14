<?php

namespace ZiffMedia\LaravelEloquentImagery\Test\Unit\Image;

use Illuminate\Support\Collection;
use ZiffMedia\LaravelEloquentImagery\Image\ImageTransformer;
use ZiffMedia\LaravelEloquentImagery\Test\Unit\AbstractTestCase;

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

