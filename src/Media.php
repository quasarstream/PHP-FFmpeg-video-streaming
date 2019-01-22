<?php

namespace AYazdanpanah\FFMpegStreaming;

use FFMpeg\Media\MediaTypeInterface;

/**
 * @method mixed save(\FFMpeg\Format\FormatInterface $format, $outputPathfile)
 * @method mixed addFilter(\FFMpeg\Filters\FilterInterface $filter)
 */
class Media
{

    protected $media;

    /**
     * Media constructor.
     * @param MediaTypeInterface $media
     */
    public function __construct(MediaTypeInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @return DASH
     */
    public function DASH(): DASH
    {
        return new DASH($this);
    }

    /**
     * @return Live
     */
    public function live(): Live
    {
        return new Live($this);
    }

    /**
     * @return HLS
     */
    public function HLS(): HLS
    {
        return new HLS($this);
    }

    /**
     * @param $argument
     * @return Media
     */
    protected function isInstanceofArgument($argument)
    {
        return ($argument instanceof $this->media) ? $this : $argument;
    }

    /**
     * @param $method
     * @param $parameters
     * @return Media
     */
    public function __call($method, $parameters)
    {
        return $this->isInstanceofArgument(
            call_user_func_array([$this->media, $method], $parameters)
        );
    }
}
