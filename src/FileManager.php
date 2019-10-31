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


use Streaming\Exception\Exception;
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
     * @throws Exception
     */
    public static function makeDir($dirname, $mode = 0777): void
    {
        $filesystem = new Filesystem();

        try {
            $filesystem->mkdir($dirname, $mode);
        } catch (IOExceptionInterface $exception) {
            throw new Exception("An error occurred while creating your directory at " . $exception->getPath(), $exception->getCode(), $exception);
        }
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
     * @return string
     * @throws Exception
     */
    public static function tmpFile(): string
    {
        return tempnam(static::tmpDirPath(), 'stream');
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function tmpDir(): string
    {
        $tmp_dir = static::tmpDirPath() . DIRECTORY_SEPARATOR . Utilities::randomString() . DIRECTORY_SEPARATOR;
        static::makeDir($tmp_dir);

        return $tmp_dir;
    }

    /**
     * @return string
     * @throws Exception
     */
    private static function tmpDirPath(): string
    {
        $tmp_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "php_ffmpeg_video_streaming";
        static::makeDir($tmp_path);

        return $tmp_path;
    }

    /**
     * @param string $source
     * @param string $destination
     * @throws Exception
     */
    public static function moveDir(string $source, string $destination)
    {
        static::makeDir($destination);
        foreach (scandir($source) as $file) {
            if (in_array($file, [".", ".."])) continue;
            if (copy($source . $file, $destination . $file)) {
                unlink($source . $file);
            }
        }

        static::deleteDirectory($source);
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