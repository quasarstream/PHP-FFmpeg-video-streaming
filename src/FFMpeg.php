<?php

namespace AYazdanpanah\FFMpegStreaming;

use FFMpeg\FFMpeg as BFFMpeg;

class FFMpeg
{

    /**
     * @param array $config
     * @param null $logger
     * @return FFMpegInstance
     */
    public static function create($config = array(), $logger = null)
    {
        return new FFMpegInstance(BFFMpeg::create($config, $logger));
    }
}
