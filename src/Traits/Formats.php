<?php

namespace AYazdanpanah\FFMpegStreaming\Traits;


use AYazdanpanah\FFMpegStreaming\Format\HEVC;
use AYazdanpanah\FFMpegStreaming\Format\Video;
use AYazdanpanah\FFMpegStreaming\Format\X264;

trait Formats
{
    /** @var Video */
    protected $format;

    /**
     * @param string $audioCodec
     * @param string $videoCodec
     * @return $this
     */
    public function X264($audioCodec = 'libmp3lame', $videoCodec = 'libx264')
    {
        $this->setFormat(new X264($audioCodec, $videoCodec));
        return $this;
    }

    /**
     * @param string $audioCodec
     * @param string $videoCodec
     * @return $this
     */
    public function HEVC($audioCodec = 'libmp3lame', $videoCodec = 'libx265')
    {
        $this->setFormat(new HEVC($audioCodec, $videoCodec));
        return $this;
    }
}