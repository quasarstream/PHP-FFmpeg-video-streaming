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

    /**
     * @param array $array
     * @param string $glue
     */
    public static function concatKeyValue(array &$array, string $glue = ""): void
    {
        array_walk($array, function (&$value, $key) use ($glue) {
            $value = "$key$glue$value";
        });
    }

    /**
     * @param array $array
     * @param string $start_with
     * @return array
     */
    public static function arrayToFFmpegOpt(array $array, string $start_with = "-"): array
    {
        $new = [];
        foreach ($array as $key => $value) {
            array_push($new, $start_with . $key, $value);
        }

        return $new;
    }

    /**
     * @return string
     */
    public static function getOS(): string
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'):
                return "osX";
            case stristr(PHP_OS, 'WIN'):
                return "windows";
            case stristr(PHP_OS, 'LINUX'):
                return "linux";
            default :
                return "unknown";
        }
    }
}