<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Streaming\FFMpeg;
use FFMpeg\Exception\ExceptionInterface;
use Streaming\Format\HEVC;
use Streaming\Format\X264;

if (!function_exists('dash')) {
    /**
     * Auto generate dash MPD file
     *
     * @param string $input_path
     * @param callable $listener
     * @param string|null $save_path
     * @return mixed
     */
    function dash(string $input_path, string $save_path = null, callable $listener = null)
    {
        $format = new HEVC();

        if (is_callable($listener)) {
            $format->on('progress', $listener);
        }

        try {
            if (filter_var($input_path, FILTER_VALIDATE_URL)) {
                $video = FFMpeg::create()->fromURL($input_path);
            } else {
                $video = FFMpeg::create()->open($input_path);
            }

            return $video->DASH()
                    ->setFormat($format)
                    ->autoGenerateRepresentations()
                    ->setAdaption('id=0,streams=v id=1,streams=a')
                    ->save($save_path);
        } catch (ExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}

if (!function_exists('hls')) {
    /**
     * Auto generate HLS M3U8 file
     *
     * @param string $input_path
     * @param string|null $save_path
     * @param callable|null $listener
     * @param string $hls_key
     * @return mixed
     */
    function hls(string $input_path, string $save_path = null, callable $listener = null, $hls_key = "")
    {
        $format = new X264();

        if (is_callable($listener)) {
            $format->on('progress', $listener);
        }

        try {
            if (filter_var($input_path, FILTER_VALIDATE_URL)) {
                $video = FFMpeg::create()->fromURL($input_path);
            } else {
                $video = FFMpeg::create()->open($input_path);
            }

            return $video->HLS()
                ->setFormat($format)
                ->autoGenerateRepresentations()
                ->setHlsKeyInfoFile($hls_key)
                ->save($save_path);
        } catch (ExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}

if (!function_exists('encrypted_hls')) {
    /**
     * Auto generate HLS M3U8 file
     *
     * @param string $input_path
     * @param string|null $save_path
     * @param callable|null $listener
     * @param string | null $url
     * @param string |null $path
     * @param string $binary
     * @return mixed
     */
    function encrypted_hls(string $input_path, string $save_path = null, callable $listener = null, $url = null, $path = null, $binary = 'openssl')
    {
        $format = new X264();

        if (is_callable($listener)) {
            $format->on('progress', $listener);
        }

        try {
            if (filter_var($input_path, FILTER_VALIDATE_URL)) {
                $video = FFMpeg::create()->fromURL($input_path);
            } else {
                $video = FFMpeg::create()->open($input_path);
            }

            return $video->HLS()
                ->setFormat($format)
                ->autoGenerateRepresentations()
                ->generateRandomKeyInfo($url, $path, $binary)
                ->save($save_path);
        } catch (ExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}