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


class Utiles
{
    /**
     * @param string $str
     * @return string
     */
    public static function appendSlash(string $str): string
    {
        return $str ? rtrim($str, '/') . "/" : $str;
    }
}