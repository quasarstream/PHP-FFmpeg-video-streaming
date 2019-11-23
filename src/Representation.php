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

class Representation
{
    private $kiloBitrate = 1000;
    private $audioKiloBitrate = null;
    private $resize = '';
    private $width = 0;
    private $height = 0;

    /**
     * @return mixed
     */
    public function getResize()
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
}