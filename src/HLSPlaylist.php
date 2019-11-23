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
     * @param $filename
     * @param $representations
     */
    public static function save(string $filename, array $representations): void
    {
        file_put_contents($filename, static::generateContents($representations, $filename));
    }

    /**
     * @param array $representations
     * @param string $filename
     * @return string
     */
    private static function generateContents(array $representations, string $filename): string
    {
        $content = ["#EXTM3U", "#EXT-X-VERSION:3"];
        foreach ($representations as $rep) {
            $content[] = "#EXT-X-STREAM-INF:BANDWIDTH=" . $rep->getKiloBitrate() * 1024 . ",RESOLUTION=" . $rep->getResize();
            $content[] = pathinfo($filename, PATHINFO_FILENAME) . "_" . $rep->getHeight() . "p.m3u8";
        }

        return implode(PHP_EOL, $content);
    }
}