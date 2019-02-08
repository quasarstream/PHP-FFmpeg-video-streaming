<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 2/9/2019
 * Time: 1:56 AM
 */

namespace AYazdanpanah\FFMpegStreaming;

use FFMpeg\FFMpeg as BFFMpeg;

class FFMpegInstance
{
    /** @var BFFMpeg */
    protected $ffmpeg;

    /**
     * FFMpegInstance constructor.
     * @param $ffmpeg
     */
    public function __construct(BFFMpeg $ffmpeg)
    {
        $this->ffmpeg = $ffmpeg;
    }

    /**
     * @param $path
     * @return Media
     */
    public function open($path): Media
    {
        return new Media($this->ffmpeg->open($path), $path);
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