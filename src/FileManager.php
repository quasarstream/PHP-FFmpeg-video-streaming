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

class FileManager
{
    private $client;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $options;

    /**
     * FileManager constructor.
     * @param string $url
     * @param string $method
     * @param array $options
     */
    public function __construct(string $url,  string $method = "GET", $options = [])
    {
        $this->client = new Client();
        $this->url = $url;
        $this->method = $method;
        $this->options = $options;
    }


    /**
     * @param string $save_to
     * @throws Exception
     */
    public function downloadFile(string $save_to): void
    {
        $this->sendRequest(array_merge($this->options, ['sink' => $save_to]));
    }

    /**
     * @param string $dir
     * @param string $name
     * @param array $headers
     * @throws Exception
     */
    public function uploadDirectory(string $dir, string $name, array $headers = []): void
    {
        $multipart = [];

        foreach (scandir($dir) as $key => $filename) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;

            if (is_file($path)) {
                $multipart[$key]['name'] = $name;
                $multipart[$key]['contents'] = fopen($path, 'r');
                if (!empty($headers)) {
                    $multipart[$key]['headers'] = $headers;
                }

                $multipart[$key]['filename'] = $filename;
            }
        }

        $this->sendRequest(array_merge($this->options, ['multipart' => array_values($multipart)]));
    }

    /**
     * @param array $options
     * @throws Exception
     */
    private function sendRequest(array $options): void
    {
        try {
            $this->client->request($this->method, $this->url, $options);
        } catch (GuzzleException $e) {

            $error = sprintf('The url("%s") is not downloadable:\n' . "\n\nExit Code: %s(%s)\n\nbody:\n: %s",
                $this->url,
                $e->getCode(),
                $e->getMessage(),
                $e->getResponse()->getBody()->getContents()
            );

            throw new Exception($error);
        }
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

        return $tmp_path . DIRECTORY_SEPARATOR . Helper::randomString() . $ext;
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