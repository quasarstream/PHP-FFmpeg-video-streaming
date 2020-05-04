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


use FFMpeg\Format\Video\DefaultVideo;
use Streaming\Exception\InvalidArgumentException;

abstract class StreamFormat extends DefaultVideo
{
    /**
     * @param int $kiloBitrate
     * @return DefaultVideo|void
     */
    public function setKiloBitrate($kiloBitrate)
    {
        throw new InvalidArgumentException("You can not set this option, use Representation instead");
    }

    /**
     * @param int $kiloBitrate
     * @return DefaultVideo|void
     */
    public function setAudioKiloBitrate($kiloBitrate)
    {
        throw new InvalidArgumentException("You can not set this option, use Representation instead");
    }
}