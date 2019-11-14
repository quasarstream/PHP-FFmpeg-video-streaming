<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Streaming\FFMpeg;


if (!function_exists('ffmpeg')) {

    /**
     * @param array $config
     * @param LoggerInterface|null $logger
     * @param FFProbe|null $probe
     * @return FFMpeg
     */
    function ffmpeg(array $config = [], LoggerInterface $logger = null, FFProbe $probe = null): FFMpeg
    {
        return FFMpeg::create($config, $logger, $probe);
    }
}