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
     * @param string $url
     * @param string|null $save_to
     * @param string $method
     * @param array $request_options
     * @throws Exception
     */
    public static function downloadFile(string $url, string $save_to = null, string $method = "GET", $request_options = []): void
    {
        $request_options = array_merge($request_options, ['sink' => $save_to]);
        $client = new Client();
        try {
            $client->request($method, $url, $request_options);
        } catch (GuzzleException $e) {

            $error = sprintf('The url("%s") is not downloadable:\n' . "\n\nExit Code: %s(%s)\n\nbody:\n: %s",
                $url,
                $e->getCode(),
                $e->getMessage(),
                $e->getResponse()->getBody()->getContents()
            );

            throw new Exception($error);
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
}