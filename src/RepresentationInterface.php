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

interface RepresentationInterface
{
    /**
     * @return string
     */
    public function size2string(): ?string;

    /**
     * @param $width
     * @param $height
     * @return Representation
     */
    public function setResize(int $width, int $height): Representation;

    /**
     * @return int
     */
    public function getKiloBitrate(): int;

    /**
     * @return int|null
     */
    public function getAudioKiloBitrate();

    /**
     * Sets the video kiloBitrate value.
     *
     * @param  integer $kiloBitrate
     * @return Representation
     */
    public function setKiloBitrate(int $kiloBitrate): Representation;

    /**
     * Sets the video kiloBitrate value.
     *
     * @param  integer $audioKiloBitrate
     * @return Representation
     */
    public function setAudioKiloBitrate(int $audioKiloBitrate): Representation;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @param array $hls_stream_info
     * @return Representation
     */
    public function setHlsStreamInfo(array $hls_stream_info): Representation;

    /**
     * @return array
     */
    public function getHlsStreamInfo(): array;

    /**
     * @param Dimension $size
     * @return Representation
     */
    public function setSize(Dimension $size): Representation;

    /**
     * @return AspectRatio
     */
    public function getRatio(): AspectRatio;
}