<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming\Clouds;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Streaming\Exception\RuntimeException;

class Cloud implements CloudInterface
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
     * @param string $url
     * @param string $method
     * @param array $options
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function __construct(string $url, string $method = "GET", $options = [])
    {
        @trigger_error(
            'Cloud class is deprecated and will be removed in a future release. Use CloudInterface instead.
            For more information see https://video.aminyazdanpanah.com/start/open-clouds and https://video.aminyazdanpanah.com/start/save-clouds',
            E_USER_DEPRECATED
        );

        $this->client = new Client();
        $this->url = $url;
        $this->method = $method;
        $this->options = $options;
    }

    /**
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        $multipart = [];

        $name = $options['name'];
        $headers = isset($options['headers']) ? $options['headers'] : [];

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
     * @param string $save_to
     * @param array $options
     */
    public function download(string $save_to, array $options = []): void
    {
        $this->sendRequest(array_merge($this->options, ['sink' => $save_to]));
    }

    /**
     * @param array $options
     * @throws RuntimeException
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
                (method_exists($e->getResponse(), 'getBody')) ? $e->getResponse()->getBody()->getContents() : ""
            );

            throw new RuntimeException($error, $e->getCode(), $e);
        }
    }
}