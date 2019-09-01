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


class KeyInfo
{
    /**
     * @param $url
     * @param $path
     * @param int $length
     * @return string
     * @throws Exception\Exception
     */
    public static function generate(string $url, string $path, int $length = 32): string
    {
        FileManager::makeDir(pathinfo($path, PATHINFO_DIRNAME));
        file_put_contents($path, openssl_random_pseudo_bytes($length));

        $key_info[] = $url;
        $key_info[] = $path;
        $key_info[] = bin2hex(openssl_random_pseudo_bytes($length));

        file_put_contents($path = FileManager::tmpFile(), implode(PHP_EOL, $key_info));

        return $path;
    }
}