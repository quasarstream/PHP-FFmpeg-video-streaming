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

use FFMpeg\Format\Audio\DefaultAudio;
use Streaming\Exception\InvalidArgumentException;

abstract class Video extends DefaultAudio
{
    /** @var string */
    protected $videoCodec;

    protected $audioKiloBitrate = null;

    /**
     * @return string
     */
    public function getVideoCodec(): string
    {
        return $this->videoCodec;
    }

    abstract protected function getAvailableVideoCodecs(): array;

    /**
     * @param string $videoCodec
     * @return Video
     */
    public function setVideoCodec(string $videoCodec): Video
    {
        if (!in_array($videoCodec, $this->getAvailableVideoCodecs())) {
            throw new InvalidArgumentException(sprintf(
                'Wrong video codec value for %s, available formats are %s'
                , $videoCodec, implode(', ', $this->getAvailableVideoCodecs())
            ));
        }

        $this->videoCodec = $videoCodec;
        return $this;
    }

    /**
     * Returns an array of extra parameters to add to ffmpeg commandline.
     *
     * @return array()
     */
    public function getExtraParams()
    {
        return [
            '-c:v',
            $this->getVideoCodec()
        ];
    }
}