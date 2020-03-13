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

use Streaming\Exception\InvalidArgumentException;

class Representation implements RepresentationInterface
{
    /** @var int $kiloBitrate video kilo bitrate */
    private $kiloBitrate;

    /** @var int $audioKiloBitrate audio kilo bitrate */
    private $audioKiloBitrate;

    /** @var string $resize WidthXHeight */
    private $resize;

    /** @var int $width video width */
    private $width;

    /** @var int $width video height */
    private $height;

    /** @var array $hls_stream_info hls stream info */
    private $hls_stream_info = [];

    /**
     * @return string
     */
    public function getResize(): string
    {
        return $this->resize;
    }

    /**
     * @param $width
     * @param $height
     * @return Representation
     * @throws InvalidArgumentException
     */
    public function setResize(int $width, int $height): Representation
    {
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Invalid resize value');
        }

        $this->width = $width;
        $this->height = $height;
        $this->resize = $width . "x" . $height;

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
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
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
}