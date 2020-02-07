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


class HLSPlaylist
{
    /**
     * @param string $filename
     * @param array $reps
     * @param string $manifests
     */
    public static function save(string $filename, array $reps, string $manifests): void
    {
        file_put_contents($filename, static::contents($reps, $manifests));
    }

    /**
     * @param array $reps
     * @param string $manifests
     * @return string
     */
    private static function contents(array $reps, string $manifests): string
    {
        $content = ["#EXTM3U", "#EXT-X-VERSION:3"];
        foreach ($reps as $rep) {
            $content[] = "#EXT-X-STREAM-INF:BANDWIDTH=" . $rep->getKiloBitrate() * 1024 . ",RESOLUTION=" . $rep->getResize();
            $content[] = $manifests . "_" . $rep->getHeight() . "p.m3u8";
        }

        return implode(PHP_EOL, $content);
    }
}