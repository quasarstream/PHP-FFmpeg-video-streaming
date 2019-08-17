<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming\MediaInfo;


use Streaming\MediaInfo\Streams\Stream;
use Streaming\MediaInfo\Streams\StreamCollection;
use Streaming\Process\Process;

class MediaInfo
{
    /**
     * @param string|null $binary
     * @param string $path
     * @return StreamCollection
     * @throws \Streaming\Exception\Exception
     */
    public static function initialize(string $path, string $binary = 'mediainfo'): StreamCollection
    {
        $media_info = json_decode(static::getJsonOutPut(new Process($binary), $path), true);

        $streams = $media_info["media"]["track"];
        $stream_collection = [];

        foreach ($streams as $stream){
            $stream_collection[] = new Stream($stream);
        }

        return new StreamCollection($stream_collection);
    }

    /**
     * @param Process $media_info
     * @param string $path
     * @return string
     * @throws \Streaming\Exception\Exception
     */
    private static function getJsonOutPut(Process $media_info, string $path): string
    {
        return $media_info->addCommand(['--Output=JSON', $path])->run();
    }
}