<?php

namespace AYazdanpanah\FFMpegStreaming\Format;

class X264 extends Video
{

    /**
     * X264 constructor.
     * @param string $audioCodec
     * @param string $videoCodec
     */
    public function __construct($audioCodec = 'libmp3lame', $videoCodec = 'libx264')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * Returns the list of available audio codecs for this format.
     *
     * @return array
     */
    public function getAvailableAudioCodecs()
    {
        return array('libmp3lame');
    }
}
