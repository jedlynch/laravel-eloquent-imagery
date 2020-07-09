<?php

use App\SingleImageExample;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        SingleImageExample::unguard();

        tap(new SingleImageExample, function ($model) {
            $model->name = 'Green Box PNG Original 150x150';
            $model->variations = [
                ['title' => 'no transformations'],
                ['title' => 'Grayscale', 'transformation' => 'grayscale'],
                ['title' => 'Fit Scale Up to 200 pixels in width', 'transformation' => 'fit_scale|size_200x'],
                ['title' => 'Fit Limit Pad Up to 500 pixels in width, extra space added has purple background', 'transformation' => 'fit_lpad|size_500x|bg_800080'],
                ['title' => 'Fit Resize to 100x50, will skew image', 'transformation' => 'fit_resize|size_100x50'],
                ['title' => 'Fit Limit to 300 in width, 200 in height - should not scale up', 'transformation' => 'fit_limit|size_300x200'],
            ];
            $model->image->setData(file_get_contents(resource_path('example-images/080-150x150.png')));
            $model->save();
        });

        tap(new SingleImageExample, function ($model) {
            $model->name = 'Red Rectangle JPG Original 500x200';
            $model->variations = [
                ['title' => 'no transformations'],
                ['title' => 'Grayscale', 'transformation' => 'grayscale'],
                ['title' => 'Fit Scale Down to 200 pixels in width', 'transformation' => 'fit_scale|size_200x'],
                ['title' => 'Fit Limit Pad Up to 800 pixels in width, extra space added has purple background', 'transformation' => 'fit_lpad|size_800x|bg_800080'],
                ['title' => 'Fit Resize to 300x100, will skew image', 'transformation' => 'fit_resize|size_300x100'],
                ['title' => 'Fit Limit to 600 in width, 300 in height - should not scale up', 'transformation' => 'fit_limit|size_600x300'],
            ];
            $model->image->setData(file_get_contents(resource_path('example-images/f00-500x200.jpg')));
            $model->save();
        });

        tap(new SingleImageExample, function ($model) {
            $model->name = 'Animated GIF Original 480x270';
            $model->variations = [
                ['title' => 'no transformations'],
                ['title' => 'Grayscale', 'transformation' => 'grayscale'],
                ['title' => 'Fit Scale Down to 200 pixels in width', 'transformation' => 'fit_scale|size_200x'],
                ['title' => 'Fit Limit Pad Up to 800 pixels in width, extra space added has purple background', 'transformation' => 'fit_lpad|size_800x|bg_800080'],
                ['title' => 'Fit Resize to 300x100, will skew image', 'transformation' => 'fit_resize|size_300x100'],
                ['title' => 'Fit Limit to 600 in width, 300 in height - should not scale up', 'transformation' => 'fit_limit|size_600x300'],
            ];
            $model->image->setData(file_get_contents(resource_path('example-images/animated-480x270.gif')));
            $model->save();
        });
    }
}
