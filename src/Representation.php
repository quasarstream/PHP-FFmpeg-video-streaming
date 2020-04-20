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

use FFMpeg\Coordinate\AspectRatio;
use FFMpeg\Coordinate\Dimension;
use Streaming\Exception\InvalidArgumentException;

class Representation implements RepresentationInterface
{
    /** @var int $kiloBitrate video kilo bitrate */
    private $kiloBitrate;

    /** @var int $audioKiloBitrate audio kilo bitrate */
    private $audioKiloBitrate;

    /** @var Dimension $size size of representation */
    private $size;

    /** @var array $hls_stream_info hls stream info */
    private $hls_stream_info = [];

    /**
     * @return string | null
     */
    public function size2string(): ?string
    {
        return !is_null($this->size) ? implode("x", [$this->getWidth(), $this->getHeight()]) : null;
    }

    /**
     * @param $width
     * @param $height
     * @return Representation
     * @throws InvalidArgumentException
     */
    public function setResize(int $width, int $height): Representation
    {
        $this->setSize(new Dimension($width, $height));
        return $this;
    }

    /**
     * @return int
     */
    public function getKiloBitrate(): int
    {
        return $this->kiloBitrate;
    }

    /**
     * @return int|null
     */
    public function getAudioKiloBitrate()
    {
        return $this->audioKiloBitrate;
    }

    /**
     * Sets the video kiloBitrate value.
     *
     * @param  integer $kiloBitrate
     * @return Representation
     * @throws InvalidArgumentException
     */
    public function setKiloBitrate(int $kiloBitrate): Representation
    {
        if ($kiloBitrate < 1) {
            throw new InvalidArgumentException('Invalid kilo bit rate value');
        }

        $this->kiloBitrate = (int)$kiloBitrate;
        return $this;
    }

    /**
     * Sets the video kiloBitrate value.
     *
     * @param  integer $audioKiloBitrate
     * @return Representation
     * @throws InvalidArgumentException
     */
    public function setAudioKiloBitrate(int $audioKiloBitrate): Representation
    {
        $this->audioKiloBitrate = $audioKiloBitrate;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->size->getWidth();
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->size->getHeight();
    }

    /**
     * @return AspectRatio
     */
    public function getRatio(): AspectRatio
    {
        return $this->size->getRatio();
    }

    /**
     * @param array $hls_stream_info
     * @return Representation
     */
    public function setHlsStreamInfo(array $hls_stream_info): Representation
    {
        $this->hls_stream_info = $hls_stream_info;
        return $this;
    }

    /**
     * @return array
     */
    public function getHlsStreamInfo(): array
    {
        return $this->hls_stream_info;
    }

    /**
     * @param Dimension $size
     * @return Representation
     */
    public function setSize(Dimension $size): Representation
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return Dimension
     */
    public function getSize(): Dimension
    {
        return $this->size;
    }
}