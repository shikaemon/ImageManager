<?php
use Shikaemon\ImageManager\Libraries\Models\Database\ImageModel;
if (!function_exists('root_path')) {
    function root_path($path)
    {
        return sprintf('%s/%s', app_path(ucfirst(config('image_manager.directory'))), $path);
    }
}
if (!function_exists('image_path')) {
    function image_path($image, $imageTypes)
    {
        if ($image instanceof ImageModel) {
            return sprintf(
                '%s/%s/%s/%s.%s',
                config('image_manager.image_path.public'),
                $image->posted_on->format('Y/m/d'),
                $image->id,
                $imageTypes,
                $image->extension
            );
        } elseif (is_array($image) && count($image) == 3) {
            return sprintf(
                '%s/%s/%s/%s.%s',
                config('image_manager.image_path.public'),
                $image[0],
                $image[1],
                $imageTypes,
                $image[2]
            );
        } else {
            return sprintf(
                '%s/no_image.png',
                config('image_manager.image_path.public')
            );
        }
    }
}
if (!function_exists('tmp_image_path')) {
    function tmp_image_path($path)
    {
        return sprintf('%s/%s', config('image_manager.image_path.tmp_image_path'), $path);
    }
}
