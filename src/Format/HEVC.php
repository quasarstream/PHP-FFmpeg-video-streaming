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

final class HEVC extends StreamFormat
{
    private const MODULUS = 2;

    /**
     * HEVC constructor.
     * @param string $video_codec
     * @param string|null $audio_codec
     */
    public function __construct(string $video_codec = 'libx265', string $audio_codec = null)
    {
        $this->setVideoCodec($video_codec);

        if ($audio_codec) {
            $this->setAudioCodec($audio_codec);
        }
    }

    /**
     * Returns the list of available audio codecs for this format.
     *
     * @return array
     */
    public function getAvailableAudioCodecs()
    {
        return ['aac', 'libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac'];
    }

    public function getAvailableVideoCodecs(): array
    {
        return ['libx265', 'h265'];
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