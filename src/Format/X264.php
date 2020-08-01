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
     * @param string $audio_codec
     * @param bool $default_init_opts
     */
    public function __construct($video_codec = 'libx264', string $audio_codec = 'aac', bool $default_init_opts = true)
    {
        $this
            ->setVideoCodec($video_codec)
            ->setAudioCodec($audio_codec);

        /**
         * set the default value of h264 codec options
         * see https://ffmpeg.org/ffmpeg-codecs.html#Options-28 for more information about options
         * return array
         */
        if ($default_init_opts) {
            $this->setAdditionalParameters([
                'bf' => 1,
                'keyint_min' => 25,
                'g' => 250,
                'sc_threshold' => 40
            ]);
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
        return ['libx264', 'h264', 'h264_afm', 'h264_nvenc'];
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