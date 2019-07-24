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

use Streaming\Exception\Exception;
use Streaming\Format\HEVC;
use Streaming\Format\Video;
use Streaming\Format\VP9;
use Streaming\Format\X264;
use FFMpeg\Format\FormatInterface;

trait Formats
{
    /** @var object */
    protected $format;

    /**
     * @param string $videoCodec
     * @return $this
     * @throws Exception
     */
    public function X264($videoCodec = 'libx264')
    {
        $this->setFormat(new X264($videoCodec));
        return $this;
    }

    /**
     * @param string $videoCodec
     * @return $this
     * @throws Exception
     */
    public function HEVC($videoCodec = 'libx265')
    {
        $this->setFormat(new HEVC($videoCodec));
        return $this;
    }

    /**
     * @param string $videoCodec
     * @return $this
     * @throws Exception
     */
    public function WebM($videoCodec = 'libvpx-vp9')
    {
        $this->setFormat(new VP9($videoCodec));
        return $this;
    }

    /**
     * @return FormatInterface|mixed
     */
    public function getFormat(): FormatInterface
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return $this
     * @throws Exception
     */
    public function setFormat($format)
    {
        if (!$format instanceof Video) {
            throw new Exception("Sorry! the format must be inherited from 'Streaming\Format\Video'");
        }

        $this->format = $format;
        return $this;
    }
}