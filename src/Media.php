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

use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Media\MediaTypeInterface;

/**
 * @method mixed save(\FFMpeg\Format\FormatInterface $format, $outputPathfile)
 * @method mixed addFilter(\FFMpeg\Filters\FilterInterface $filter)
 * @method mixed getStreams()
 */
class Media
{

    protected $media;
    /**
     * @var string
     */
    private $path;

    /**
     * Media constructor.
     * @param MediaTypeInterface $media
     * @param string $path
     */
    public function __construct(MediaTypeInterface $media, string $path)
    {
        $this->media = $media;
        $this->path = $path;
    }

    /**
     * @return DASH
     */
    public function DASH(): DASH
    {
        return new DASH($this);
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

    /**
     * @return mixed
     */
    public function getFirstStream(): Stream
    {
        return $this->media->getStreams()->first();
    }

    /**
     * @return array
     */
    public function getPathInfo(): array
    {
        return pathinfo($this->path);
    }
}
