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


use Streaming\Exception\RuntimeException;

class HLSKeyInfo
{
    /**
     * @param string $path
     * @param string $url
     * @param string $key_info_path
     * @param int $length
     * @return string
     */
    public static function generate(string $path, string $url, string $key_info_path = null, int $length = 16): string
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException('OpenSSL is not installed.');
        }

        File::makeDir(dirname($path));
        file_put_contents($path, openssl_random_pseudo_bytes($length));

        file_put_contents(
            $path_f = $key_info_path ?? File::tmp(),
            implode(
                PHP_EOL,
                [$url, $path, bin2hex(openssl_random_pseudo_bytes($length))]
            )
        );

        return $path_f;
    }
}