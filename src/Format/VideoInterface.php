<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming\Format;

use FFMpeg\Format\AudioInterface;

interface VideoInterface extends AudioInterface
{
    /**
     * Returns the modulus used by the Resizable video.
     *
     * This used to calculate the target dimensions while maintaining the best
     * aspect ratio.
     *
     * @see http://www.undeadborn.net/tools/rescalculator.php
     *
     * @return integer
     */
    public function getModulus();

    /**
     * Returns the video codec.
     *
     * @return string
     */
    public function getVideoCodec();

    /**
     * Returns true if the current format supports B-Frames.
     *
     * @see https://wikipedia.org/wiki/Video_compression_picture_types
     *
     * @return Boolean
     */
    public function supportBFrames();

    /**
     * Returns the list of available video codecs for this format.
     *
     * @return array
     */
    public function getAvailableVideoCodecs();
}