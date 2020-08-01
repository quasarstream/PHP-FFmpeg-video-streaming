<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming\Filters;

use Streaming\StreamInterface;

class HLSFilterV2 extends FormatFilter
{
    /**
     * This is a new version of HLSFilter that the master playlist will be created by FFmpeg
     *
     *
     *
     * ffmpeg ^
     * -i path/to/video ^
     * -i path/to/video  ^
     * -i path/to/video  ^
     * -c:v libx264 -c:a copy ^
     * -s:v:0 1920x1080 -b:v:0 4096k -s:v:1 1280x720 -b:v:1 2048k -s:v:2 854x480 -b:v:2 750k ^
     * -map 0:a -map 0:v -map 1:v -map 2:v ^
     * -var_stream_map "a:0,agroup:audio v:0,agroup:audio v:1,agroup:audio v:2,agroup:audio" ^
     * -f hls -hls_segment_type mpegts -hls_list_size 0 -hls_time 5 -hls_allow_cache 0 -master_pl_name master-playlist.m3u8 -y playlist%v.m3u8
     *
     *
     *
     * */

    /**
     * @param StreamInterface $stream
     * @return mixed
     */
    public function streamFilter(StreamInterface $stream): void
    {
        // TODO: Implement streamFilter() method.
        // add Mapping audio and video stream #60
    }
}