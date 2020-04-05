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

/**
 * The VP9 video format
 */
final class VP9 extends StreamFormat
{
    private const MODULUS = 2;

    /**
     * VP9 constructor.
     * @param string $video_codec
     * @param string|null $audio_codec
     */
    public function __construct(string $video_codec = 'libvpx-vp9', string $audio_codec = null)
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
        return ['libvorbis'];
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs(): array
    {
        return ['libvpx', 'libvpx-vp9'];
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
