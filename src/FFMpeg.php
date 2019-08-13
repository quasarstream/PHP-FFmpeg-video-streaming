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

use FFMpeg\FFMpeg as BFFMpeg;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Streaming\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

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
     * @throws Exception
     */
    public function open(string $path, bool $is_tmp = false): Media
    {
        if (!is_file($path)) {
            throw new Exception("There is no file in this path: " . $path);
        }

        return new Media($this->ffmpeg->open($path), $path, $is_tmp);
    }

    /**
     * @param string $url
     * @param string|null $save_to
     * @param string $method
     * @param $request_options
     * @return Media
     * @throws Exception
     */
    public function fromURL(string $url, string $save_to = null, string $method = "GET", $request_options = []): Media
    {
        $is_tmp = false;

        if (null === $save_to) {
            $is_tmp = true;
            $tmp_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "php_ffmpeg_video_streaming";

            Helper::makeDir($tmp_path);

            $ext = "";
            if (isset(pathinfo($url)["extension"])) {
                $ext = "." . substr(explode("?", pathinfo($url)["extension"])[0], 0, 10);
            }

            $save_to = $tmp_path . DIRECTORY_SEPARATOR . Helper::randomString() . $ext;
        }

        Helper::downloadFile($url, $save_to, $method, $request_options);

        return $this->open($save_to, $is_tmp);
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
    public static function create($config = array(), LoggerInterface $logger = null, FFProbe $probe = null)
    {
        return new static(BFFMpeg::create($config, $logger, $probe));
    }
}