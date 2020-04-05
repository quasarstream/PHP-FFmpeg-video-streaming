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

final class X264 extends StreamFormat
{
    private const MODULUS = 2;

    /**
     * X264 constructor.
     * @param string $video_codec
     * @param null $audio_codec
     */
    public function __construct($video_codec = 'libx264', $audio_codec = null)
    {
        $this->setVideoCodec($video_codec);

        if ($audio_codec) {
            $this->setAudioCodec($audio_codec);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return ['aac', 'libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac'];
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs()
    {
        return ['libx264', 'h264', 'h264_afm'];
    }

    /**
     * @return int
     */
    public function getModulus()
    {
        return static::MODULUS;
    }

    /**
     * Returns true if the current format supports B-Frames.
     *
     * @see https://wikipedia.org/wiki/Video_compression_picture_types
     *
     * @return Boolean
     */
    public function supportBFrames()
    {
        return true;
    }
}