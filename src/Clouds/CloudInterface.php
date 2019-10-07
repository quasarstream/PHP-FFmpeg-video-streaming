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


interface CloudInterface
{
    /**
     * Upload a entire directory to a cloud
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options): void;

    /**
     * Download a file from a cloud
     * @param string $save_to
     * @param array $options
     */
    public function download(string $save_to, array $options): void;
}