<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming\Traits;

use Streaming\Exception\InvalidArgumentException;
use Streaming\Format\HEVC;
use Streaming\Format\Video;
use Streaming\Format\VideoInterface;
use Streaming\Format\VP9;
use Streaming\Format\X264;

trait Formats
{
    /** @var VideoInterface */
    protected $format;

    /**
     * @param string $video_codec
     * @param string|null $audio_codec
     * @return $this
     */
    public function X264(string $video_codec = 'libx264', string $audio_codec = null)
    {
        $this->setFormat(new X264($video_codec, $audio_codec));
        return $this;
    }

    /**
     * @param string $video_codec
     * @param string|null $audio_codec
     * @return $this
     */
    public function HEVC(string $video_codec = 'libx265', string $audio_codec = null)
    {
        $this->setFormat(new HEVC($video_codec, $audio_codec));
        return $this;
    }

    /**
     * @param string $video_codec
     * @param string|null $audio_codec
     * @return $this
     */
    public function WebM(string $video_codec = 'libvpx-vp9', string $audio_codec = null)
    {
        $this->setFormat(new VP9($video_codec, $audio_codec));
        return $this;
    }

    /**
     * @return VideoInterface
     */
    public function getFormat(): VideoInterface
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFormat($format)
    {
        if (!$format instanceof Video) {
            throw new InvalidArgumentException("Sorry! the format must be instance of 'Streaming\Format\Video' object");
        }

        $this->format = $format;
        return $this;
    }
}