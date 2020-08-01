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
     * @param bool $default_init_opts
     */
    public function __construct(string $video_codec = 'libx265', string $audio_codec = 'aac', bool $default_init_opts = true)
    {
        $this
            ->setVideoCodec($video_codec)
            ->setAudioCodec($audio_codec);

        /**
         * set the default value of h265 codec options
         * see https://ffmpeg.org/ffmpeg-codecs.html#Options-29 for more information about options
         */
        if ($default_init_opts) {
            $this->setAdditionalParameters([
                'keyint_min' => 25,
                'g' => 250,
                'sc_threshold' => 40
            ]);
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
        return ['libx265', 'h265', 'hevc_nvenc'];
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