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
     * @return string
     * @throws Exception\Exception
     */
    public static function generate($url, $path): string
    {
        FileManager::makeDir(pathinfo($path, PATHINFO_DIRNAME));
        file_put_contents($path, openssl_random_pseudo_bytes(32));

        $key_info[] = $url;
        $key_info[] = $path;
        $key_info[] = bin2hex(openssl_random_pseudo_bytes(32));

        file_put_contents($path = FileManager::tmpFile(), implode(PHP_EOL, $key_info));

        return $path;
    }
}