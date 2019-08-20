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



use Streaming\Exception\InvalidArgumentException;

class Helper
{
    /**
     * round a number to nearest even number
     *
     * @param float $number
     * @return int
     */
    public static function roundToEven(float $number): int
    {
        return (($number = intval($number)) % 2 == 0) ? $number : $number + 1;
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public static function randomString($length = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 1, $length);
    }

    /**
     * @param $word
     * @return bool|string
     */
    public static function appendSlash(string $word)
    {
        if ($word) {
            return rtrim($word, '/') . '/';
        }
        return $word;
    }

    /**
     * @param $url
     * @return bool
     */
    public static function isURL(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Your URL($url) is not valid! Your URL should start with (http://) or (https://).");
        }

        return true;
    }
}