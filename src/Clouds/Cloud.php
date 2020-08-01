<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming\Clouds;


use Streaming\Exception\InvalidArgumentException;
use Streaming\File;

class Cloud
{
    /**
     * @param array $clouds
     * @param string $tmp_dir
     */
    public static function uploadDirectory(array $clouds, string $tmp_dir): void
    {
        if (isset($clouds['cloud'])) {
            $clouds = [$clouds];
        }

        foreach ($clouds as $cloud) {
            static::transfer($cloud, __FUNCTION__, $tmp_dir);
        }
    }

    /**
     * @param array $cloud
     * @param string|null $save_to
     * @return array
     */
    public static function download(array $cloud, string $save_to = null): array
    {
        $prefix = $cloud['options']['Key'] ?? $cloud['options']['object_name'] ?? $cloud['options']['blob'] ?? uniqid('stream_', true);
        list($save_to, $is_tmp) = $save_to ? [$save_to, false] : [File::tmp($prefix), true];
        static::transfer($cloud, __FUNCTION__, $save_to);

        return [$save_to, $is_tmp];
    }

    /**
     * @param $cloud_c
     * @param $method
     * @param $path
     */
    private static function transfer(array $cloud_c, string $method, string $path): void
    {
        extract($cloud_c);
        if (isset($cloud) && $cloud instanceof CloudInterface) {
            call_user_func_array([$cloud, $method], [$path, $options ?? []]);
        } else {
            throw new InvalidArgumentException('The cloud key must be instance of the CloudInterface');
        }
    }
}