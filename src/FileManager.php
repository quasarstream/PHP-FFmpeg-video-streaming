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
 * FileManager constructor.
 * It is all about files
*/

class FileManager
{
    /**
     * @param $dirname
     * @param int $mode
     */
    public static function makeDir(string $dirname, int $mode = 0777): void
    {
        $fs = new Filesystem();

        try {
            $fs->mkdir($dirname, $mode);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException("Failed to make the directory at " . $e->getPath(), $e->getCode(), $e);
        }
    }

    /**
     * @param $dir
     * @return int|null
     */
    public static function directorySize(string $dir)
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
        static::makeDir($dst);

        foreach (scandir($src) as $file) {
            if (in_array($file, [".", ".."])) continue;

            if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                static::moveDir($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                continue;
            }

            copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
        }

        static::deleteDirectory($src);
    }

    /**
     * @param $dir
     */
    public static function deleteDirectory(string $dir): void
    {
        $fs = new Filesystem();

        try {
            $fs->remove($dir);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException("Failed to remove the directory at " . $e->getPath(), $e->getCode(), $e);
        }
    }
}