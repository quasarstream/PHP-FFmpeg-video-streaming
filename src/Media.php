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

use FFMpeg\Media\MediaTypeInterface;

/**
 * @method \FFMpeg\Media\Video save(\FFMpeg\Format\FormatInterface $format, $outputPathfile)
 * @method \FFMpeg\Media\Video addFilter(\FFMpeg\Filters\FilterInterface $filter)
 * @method \FFMpeg\FFProbe\DataMapping\Format getFormat()
 * @method \FFMpeg\FFProbe\DataMapping\StreamCollection getStreams()
 */
class Media
{

    protected $media;
    /**
     * @var string
     */
    private $path;
    /**
     * @var bool
     */
    private $is_tmp;

    /**
     * Media constructor.
     * @param MediaTypeInterface $media
     * @param string $path
     * @param bool $is_tmp
     */
    public function __construct(MediaTypeInterface $media, string $path, bool $is_tmp)
    {
        $this->media = $media;
        $this->path = $path;
        $this->is_tmp = $is_tmp;
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
     * @return StreamToFile
     */
    public function stream2file(): StreamToFile
    {
        return new StreamToFile($this);
    }

    /**
     * @param $argument
     * @return Media
     */
    private function isInstanceofArgument($argument)
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
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    public function isTmp(): bool
    {
        return $this->is_tmp;
    }
}