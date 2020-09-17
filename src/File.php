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
     * @param $path
     * @param $content
     * @param bool $force
     * @return void
     */
    public static function put($path, $content, $force = true): void
    {
        if (file_exists($path) && !$force) {
            throw new RuntimeException("File Already Exists");
        }

        if (false === @file_put_contents($path, $content)) {
            throw new RuntimeException("Unable to save the file");
        }
    }

    /**
     * @param string $prefix
     * @return string
     */
    public static function tmp($prefix = 'pfvs.file_'): string
    {
        for ($i = 0; $i < 10; ++$i) {
            $tmpFile = static::tmpDirPath() . '/' . basename($prefix) . uniqid(mt_rand());
            $handle = @fopen($tmpFile, 'x+');

            if (false === $handle) {
                continue;
            }

            @fclose($handle);

            return $tmpFile;
        }

        throw new RuntimeException("A temporary file could not be created.");
    }

    /**
     * @return string
     */
    public static function tmpDir(): string
    {
        static::makeDir($tmp_dir = static::tmpDirPath() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR);
        return $tmp_dir;
    }

    /**
     * clear all tmp files
     */
    public static function cleanTmpFiles(): void
    {
        static::remove(static::tmpDirPath());
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
    public static function move(string $src, string $dst): void
    {
        static::filesystem('mirror', [$src, $dst]);
        static::remove($src);
    }

    /**
     * @param string $src
     * @param string $dst
     * @param bool $force
     */
    public static function copy(string $src, string $dst, bool $force = true): void
    {
        static::filesystem('copy', [$src, $dst, $force]);
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
     * @return mixed
     */
    private static function filesystem(string $method, array $params)
    {
        try {
            return \call_user_func_array([new Filesystem, $method], $params);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException("Failed action" . $e->getPath(), $e->getCode(), $e);
        }
    }
}