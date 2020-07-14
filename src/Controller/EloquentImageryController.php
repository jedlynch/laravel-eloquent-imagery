<?php

namespace ZiffMedia\LaravelEloquentImagery\Controller;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use ZiffMedia\LaravelEloquentImagery\Image\ImageTransformer;
use ZiffMedia\LaravelEloquentImagery\Image\PlaceholderImageFactory;

class EloquentImageryController extends Controller
{
    public function render(Request $request)
    {
        $path = $request->route('path');

        $cacheEnabled = config('eloquent-imagery.render.caching.enable', false);
        $cacheDriver = config('eloquent-imagery.render.caching.driver', 'disk');

        if ($cacheEnabled && Cache::has($path)) {
            return Cache::store($cacheDriver)->get($path);
        }

        // Path traversal detection: 404 the user, no need to give additional information
        abort_if((in_array($path[0], ['.', '/']) || strpos($path, '../') !== false), 404);

        $disk = config('eloquent-imagery.filesystem', config('filesystems.default'));

        /** @var Filesystem $filesystem */
        $filesystem = app(FilesystemManager::class)->disk($disk);

        $imageRequest = ImageRequest::create($request);

        // assume the mime type is PNG unless otherwise specified
        $mimeType = 'image/png';
        $imageBytes = null;

        // step 1: if placeholder request, generate a placeholder
        // if ($filenameWithoutExtension === config('eloquent-imagery.render.placeholder.filename') && config('eloquent-imagery.render.placeholder.enable')) {
        if ($imageRequest->isPlaceholderRequest()) {
            list ($placeholderWidth, $placeholderHeight) = isset($modifierOperators['size']) ? explode('x', $modifierOperators['size']) : [400, 400];
            $imageBytes = (new PlaceholderImageFactory())->create($placeholderWidth, $placeholderHeight, $modifierOperators['bgcolor'] ?? null);
        }

        // step 2: no placeholder, look for actual file on designated filesystem
        if (!$imageBytes) {
            try {
                $imageBytes = $filesystem->get($imageRequest->imagePath);
                $mimeType = $filesystem->getMimeType($imageRequest->imagePath);
            } catch (FileNotFoundException $e) {
                $imageBytes = null;
            }
        }

        // step 3: no placeholder, no primary FS image, look for fallback image on alternative filesystem if enabled
        if (!$imageBytes && config('eloquent-imagery.render.fallback.enable')) {
            /** @var Filesystem $fallbackFilesystem */
            $fallbackFilesystem = app(FilesystemManager::class)->disk(config('eloquent-imagery.render.fallback.filesystem'));
            try {
                $imageBytes = $fallbackFilesystem->get($imageRequest->imagePath);
                $mimeType = $fallbackFilesystem->getMimeType($imageRequest->imagePath);
                if (config('eloquent-imagery.render.fallback.mark_images')) {
                    $imageModifier = new ImageTransformer();
                    $imageBytes = $imageModifier->addFromFallbackWatermark($imageBytes);
                }
            } catch (FileNotFoundException $e) {
                $imageBytes = null;
            }
        }

        // step 4: no placeholder, no primary FS image, no fallback, generate a placeholder if enabled for missing files
        if (!$imageBytes && config('eloquent-imagery.render.placeholder.use_for_missing_files') === true) {
            list ($placeholderWidth, $placeholderHeight) = isset($modifierOperators['size']) ? explode('x', $modifierOperators['size']) : [400, 400];
            $imageBytes = (new PlaceholderImageFactory())->create($placeholderWidth, $placeholderHeight, $modifierOperators['bgcolor'] ?? null);
        }

        abort_if(!$imageBytes, 404); // no image, no fallback, no placeholder

        $imageBytes = ImageTransformer::createDefault()->transform($imageRequest->imageModifiers, $imageBytes);

        $browserCacheMaxAge = config('eloquent-imagery.render.browser_cache_max_age');

        $response = response()
            ->make($imageBytes)
            ->header('Content-type', $mimeType)
            ->header('Cache-control', "public, max-age=$browserCacheMaxAge");

        if ($cacheEnabled) {
            Cache::store($cacheDriver)->put($path, $response, config('eloquent-imagery.render.caching.ttl', 60));
        }

        return $response;
    }
}
