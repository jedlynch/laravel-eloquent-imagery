<?php

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Filesystem\FilesystemManager;

if (! function_exists('eloquent_imagery_url')) {
    /**
     * Apply modifiers to a url
     * @param $path
     * @param $modifiers
     */
    function eloquent_imagery_url($relativePath, array $modifiers = []) {
        static $renderRouteEnabled = null;
        static $imageryFilesystem = null;

        if ($renderRouteEnabled === null) {
            config('eloquent-imagery.render.enable');
        }

        if ($imageryFilesystem === null) {
            $imageryFilesystem = app(FilesystemManager::class)->disk(config('eloquent-imagery.filesystem', config('filesystems.default')));
        }

        if ($renderRouteEnabled === false && $modifiers) {
            throw new RuntimeException('Cannot process render options unless the rendering route is enabled');
        }

        if ($renderRouteEnabled === false && $imageryFilesystem instanceof Cloud) {
            return $imageryFilesystem->url($relativePath);
        }

        if ($modifiers) {
            $modifierParts = explode('|', $modifiers);
            sort($modifierParts);
            $modifiers = implode('.', $modifierParts);
            $modifiers = str_replace(':', '_', $modifiers);
        }

        // keyed with [dirname, filename, basename, extension]
        $pathinfo = pathinfo($relativePath);

        $pathWithModifiers =
            (($pathinfo['dirname'] !== '.') ? ($pathinfo['dirname'] . '/') : '')
            . $pathinfo['filename']
            . ($modifiers ? ('.' . $modifiers) : '')
            . '.' . $pathinfo['extension'];

        return url()->route('eloquent-imagery.render', $pathWithModifiers);
    }
}
