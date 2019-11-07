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
use Streaming\FileManager;

class CloudManager
{
    /**
     * @param array $clouds
     * @param string $tmp_dir
     */
    public static function uploadDirectory(?array $clouds, ?string $tmp_dir): void
    {
        if (!$clouds) {
            return;
        }

        if (!is_array(current($clouds))) {
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
        list($save_to, $is_tmp) = $save_to ? [$save_to, false] : [FileManager::tmpFile(), true];
        static::transfer($cloud, __FUNCTION__, $save_to);

        return [$save_to, $is_tmp];
    }

    /**
     * @param $cloud
     * @param $method
     * @param $path
     */
    private static function transfer($cloud, $method, $path): void
    {
        if (is_array($cloud) && $cloud['cloud'] instanceof CloudInterface) {
            $options = (isset($cloud['options']) && is_array($cloud['options'])) ? $cloud['options'] : [];
            call_user_func_array([$cloud['cloud'], $method], [$path, $options]);
        } else {
            throw new InvalidArgumentException('You must pass an array of clouds to the save method. 
                and the cloud must be instance of CloudInterface');
        }
    }
}