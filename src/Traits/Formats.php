<?php

namespace AYazdanpanah\FFMpegStreaming\Traits;


use AYazdanpanah\FFMpegStreaming\Format\HEVC;
use AYazdanpanah\FFMpegStreaming\Format\X264;
use FFMpeg\Format\FormatInterface;

trait Formats
{
    /** @var object */
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

    /**
     * @return FormatInterface|mixed
     */
    private function getFormat(): FormatInterface
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return mixed
     */
    protected function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
}