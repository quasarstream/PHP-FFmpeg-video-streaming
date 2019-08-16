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


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Streaming\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class Helper
{
    /**
     * round a number to nearest even number
     *
     * @param float $number
     * @return int
     */
    public static function roundToEven(float $number): int
    {
        return (($number = intval($number)) % 2 == 0) ? $number : $number + 1;
    }

    /**
     * @param $dirname
     * @param int $mode
     * @throws Exception
     */
    public static function makeDir($dirname, $mode = 0777): void
    {
        $filesystem = new Filesystem();

        try {
            $filesystem->mkdir($dirname, $mode);
        } catch (IOExceptionInterface $exception) {
            throw new Exception("An error occurred while creating your directory at " . $exception->getPath());
        }
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public static function randomString($length = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 1, $length);

    }

    /**
     * @param $dir
     * @return int|null
     */
    public static function directorySize($dir)
    {
        if (is_dir($dir)) {
            $size = 0;
            foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
                $size += is_file($each) ? filesize($each) : static::directorySize($each);
            }
            return $size;
        }

        return null;
    }

    /**
     * @param string $ext
     * @return string
     * @throws Exception
     */
    public static function tmpFile(string $ext = ""): string
    {
        if ("" !== $ext) {
            $ext = "." . $ext;
        }

        $tmp_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "php_ffmpeg_video_streaming";
        static::makeDir($tmp_path);

        return $tmp_path . DIRECTORY_SEPARATOR . static::randomString() . $ext;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function tmpDir(): string
    {
        return static::tmpFile() . DIRECTORY_SEPARATOR;
    }


    public static function moveDir(string $source, string $destination)
    {
        foreach (scandir($source) as $file) {
            if (in_array($file, [".", ".."])) continue;
            if (copy($source . $file, $destination . $file)) {
                unlink($source . $file);
            }
        }
    }

    /**
     * @param $dir
     * @return bool
     */
    public static function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return @unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if (in_array($item, [".", ".."])) continue;
            if (!static::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return @rmdir($dir);
    }
}