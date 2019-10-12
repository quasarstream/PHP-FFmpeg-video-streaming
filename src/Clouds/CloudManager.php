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
    public static function saveToClouds(?array $clouds, ?string $tmp_dir): void
    {
        if (!$clouds) {
            return;
        }

        if (!is_array(current($clouds))) {
            $clouds = [$clouds];
        }

        sleep(1);

        foreach ($clouds as $cloud) {
            if (is_array($cloud) && $cloud['cloud'] instanceof CloudInterface) {
                $cloud_obj = $cloud['cloud'];
                $options = (isset($cloud['options']) && is_array($cloud['options'])) ? $cloud['options'] : [];

                $cloud_obj->uploadDirectory($tmp_dir, $options);
            } else {
                throw new InvalidArgumentException('You must pass an array of clouds to the save method. 
                and the cloud must be instance of CloudInterface');
            }
        }

    }

    /**
     * @param array $cloud
     * @param string|null $save_to
     * @return array
     * @throws \Streaming\Exception\Exception
     */
    public static function downloadFromCloud(array $cloud, string $save_to = null): array
    {
        list($is_tmp, $save_to) = static::isTmp($save_to);

        if (is_array($cloud) && $cloud['cloud'] instanceof CloudInterface) {
            $cloud_obj = $cloud['cloud'];
            $options = (isset($cloud['options']) && is_array($cloud['options'])) ? $cloud['options'] : [];

            $cloud_obj->download($save_to, $options);
        } else {
            throw new InvalidArgumentException('You must pass an array of a cloud to the openFromCloud method. 
                    and the cloud must be instance of CloudInterface');
        }

        return [$save_to, $is_tmp];
    }

    /**
     * @param $path
     * @return array
     * @throws \Streaming\Exception\Exception
     */
    private static function isTmp($path)
    {
        $is_tmp = false;

        if (null === $path) {
            $is_tmp = true;
            $path = FileManager::tmpFile();
        }

        return [$is_tmp, $path];
    }

}