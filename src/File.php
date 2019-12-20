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


use Streaming\Exception\RuntimeException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * File constructor.
 * It is all about files
*/

class File
{
    /**
     * @param $dirname
     * @param int $mode
     */
    public static function makeDir(string $dirname, int $mode = 0777): void
    {
        static::filesystem('mkdir', [$dirname, $mode]);
    }

    /**
     * @param $dir
     * @return int|null
     */
    public static function directorySize(string $dir): int
    {
        if (is_dir($dir)) {
            $size = 0;
            foreach (scandir($dir) as $file) {
                if (in_array($file, [".", ".."])) continue;
                $filename = $dir . DIRECTORY_SEPARATOR . $file;
                $size += is_file($filename) ? filesize($filename) : static::directorySize($filename);
            }
            return $size;
        }

        return 0;
    }

    /**
     * @return string
     */
    public static function tmpFile(): string
    {
        return tempnam(static::tmpDirPath(), 'stream');
    }

    /**
     * @return string
     */
    public static function tmpDir(): string
    {
        static::makeDir($tmp_dir = static::tmpDirPath() . DIRECTORY_SEPARATOR . Utilities::randomString() . DIRECTORY_SEPARATOR);
        return $tmp_dir;
    }

    /**
     * @return string
     */
    private static function tmpDirPath(): string
    {
        static::makeDir($tmp_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "php_ffmpeg_video_streaming");
        return $tmp_path;
    }

    /**
     * @param string $src
     * @param string $dst
     */
    public static function moveDir(string $src, string $dst): void
    {
        static::filesystem('mirror', [$src, $dst]);
        static::remove($src);
    }

    /**
     * @param $dir
     */
    public static function remove(string $dir): void
    {
        static::filesystem('remove', [$dir]);
    }

    /**
     * @param string $method
     * @param array $params
     */
    private static function filesystem(string $method, array $params): void
    {
        try {
            \call_user_func_array([new Filesystem, $method], $params);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException("Failed action" . $e->getPath(), $e->getCode(), $e);
        }
    }
}