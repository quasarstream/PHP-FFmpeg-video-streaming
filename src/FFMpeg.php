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

use FFMpeg\Exception\ExceptionInterface;
use FFMpeg\FFMpeg as BFFMpeg;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Streaming\Clouds\AWS;
use Streaming\Clouds\Cloud;
use Streaming\Clouds\CloudInterface;
use Streaming\Clouds\GoogleCloudStorage;
use Streaming\Clouds\MicrosoftAzure;
use Streaming\Exception\Exception;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

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
     */
    public function open(string $path, bool $is_tmp = false): Media
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException("There is no file in this path: " . $path);
        }

        try {
            return new Media($this->ffmpeg->open($path), $path, $is_tmp);
        } catch (ExceptionInterface $e) {
            @unlink($path);
            throw new RuntimeException(sprintf("There was an error opening this file: \n\n reason: \n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param array $cloud
     * @param string|null $save_to
     * @return Media
     * @throws Exception
     */
    public function openFromCloud(array $cloud, string $save_to = null): Media
    {
        list($is_tmp, $save_to) = $this->isTmp($save_to);

        if (is_array($cloud) && $cloud['cloud'] instanceof CloudInterface) {
            $cloud_obj = $cloud['cloud'];
            $options = (isset($cloud['options']) && is_array($cloud['options'])) ? $cloud['options'] : [];

            $cloud_obj->download($save_to, $options);
        } else {
            throw new InvalidArgumentException('You must pass an array of a cloud to the openFromCloud method. 
                    and the cloud must be instance of CloudInterface');
        }

        return $this->open($save_to, $is_tmp);
    }

    /**
     * @param $path
     * @return array
     * @throws Exception
     */
    private function isTmp($path)
    {
        $is_tmp = false;

        if (null === $path) {
            $is_tmp = true;
            $path = FileManager::tmpFile();
        }

        return [$is_tmp, $path];
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

    /**
     * @param string $url
     * @param string|null $save_to
     * @param string $method
     * @param $request_options
     * @return Media
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function fromURL(string $url, string $save_to = null, string $method = "GET", array $request_options = []): Media
    {
        @trigger_error('fromURL method is deprecated and will be removed in a future release. Use Cloud instead', E_USER_DEPRECATED);

        Helper::isURL($url);
        list($is_tmp, $save_to) = $this->isTmp($save_to);

        $cloud = new Cloud($url, $method, $request_options);
        $cloud->download($save_to);

        return $this->open($save_to, $is_tmp);
    }

    /**
     * @param array $config
     * @param string $bucket
     * @param string $key
     * @param string|null $save_to
     * @return Media
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function fromS3(array $config, string $bucket, string $key, string $save_to = null): Media
    {
        @trigger_error('fromS3 method is deprecated and will be removed in a future release. Use AWS instead', E_USER_DEPRECATED);

        list($is_tmp, $save_to) = $this->isTmp($save_to);

        $aws = new AWS($config);
        $aws->download($save_to, ['bucket' => $bucket, 'key' => $key]);

        return $this->open($save_to, $is_tmp);
    }

    /**
     * @param array $config
     * @param string $bucket
     * @param string $name
     * @param string|null $save_to
     * @param bool $userProject
     * @return Media
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function fromGCS(array $config, string $bucket, string $name, string $save_to = null, $userProject = false): Media
    {
        @trigger_error('fromMAS method is deprecated and will be removed in a future release. Use MicrosoftAzure instead', E_USER_DEPRECATED);
        list($is_tmp, $save_to) = $this->isTmp($save_to);

        $google_cloud = new GoogleCloudStorage($config, $bucket, $userProject);
        $google_cloud->download($save_to, ['name' => $name]);

        return $this->open($save_to, $is_tmp);
    }

    /**
     * @param string $connectionString
     * @param string $container
     * @param string $blob
     * @param string|null $save_to
     * @return Media
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function fromMAS(string $connectionString, string $container, string $blob, string $save_to = null): Media
    {
        @trigger_error('fromMAS method is deprecated and will be removed in a future release. Use MicrosoftAzure instead', E_USER_DEPRECATED);
        list($is_tmp, $save_to) = $this->isTmp($save_to);

        $google_cloud = new MicrosoftAzure($connectionString);
        $google_cloud->download($save_to, ['container' => $container, 'blob' => $blob]);

        return $this->open($save_to, $is_tmp);
    }
}