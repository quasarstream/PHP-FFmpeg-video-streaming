<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming;


use FFMpeg\Format\VideoInterface;

interface StreamInterface
{
    /**
     * @return Media
     */
    public function getMedia(): Media;

    /**
     * @return VideoInterface
     */
    public function getFormat(): VideoInterface;

    /**
     * @param int $option
     * @return string
     */
    public function pathInfo(int $option): string;

    /**
     * @param string $path
     * @param array $clouds
     * @return mixed
     */
    public function save(string $path = null, array $clouds = []): Stream;

    /**
     * @param string $url
     */
    public function live(string $url): void;
}