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
}