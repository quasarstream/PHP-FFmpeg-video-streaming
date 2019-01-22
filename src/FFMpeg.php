<?php

namespace AYazdanpanah\FFMpegStreaming;

use FFMpeg\FFMpeg as BFFMpeg;

class FFMpeg
{

    protected $ffmpeg;

    /**
     * FFMpeg constructor.
     * @param $config
     * @param null $logger
     */
    public function __construct($config, $logger = null)
    {
        $this->ffmpeg = BFFMpeg::create($config, $logger);
    }

    /**
     * @param $path
     * @return Media
     */
    public function open($path): Media
    {
        return new Media($this->ffmpeg->open($path));
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ffmpeg, $method], $parameters);
    }
}
