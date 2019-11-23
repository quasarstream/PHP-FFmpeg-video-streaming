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

use FFMpeg\Exception\ExceptionInterface;
use FFMpeg\FFMpeg as BFFMpeg;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Streaming\Clouds\Cloud;
use Streaming\Exception\RuntimeException;


class FFMpeg
{
    /** @var BFFMpeg */
    protected $ffmpeg;

    /**
     * @param $ffmpeg
     */
    public function __construct(BFFMpeg $ffmpeg)
    {
        $this->ffmpeg = $ffmpeg;
    }

    /**
     * @param string $path
     * @param bool $is_tmp
     * @return Media
     */
    public function open(string $path, bool $is_tmp = false): Media
    {
        try {
            return new Media($this->ffmpeg->open($path), $path, $is_tmp);
        } catch (ExceptionInterface $e) {
            if ($is_tmp) {
                sleep(1);
                @unlink($path);
            }
            throw new RuntimeException("An error occurred while opening the file: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $cloud
     * @param string|null $save_to
     * @return Media
     */
    public function openFromCloud(array $cloud, string $save_to = null): Media
    {
        return call_user_func_array([$this, 'open'], Cloud::download($cloud, $save_to));
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->ffmpeg, $method], $parameters);
    }

    /**
     * @param array $config
     * @param LoggerInterface $logger
     * @param FFProbe|null $probe
     * @return FFMpeg
     */
    public static function create(array $config = [], LoggerInterface $logger = null, FFProbe $probe = null)
    {
        return new static(BFFMpeg::create($config, $logger, $probe));
    }
}