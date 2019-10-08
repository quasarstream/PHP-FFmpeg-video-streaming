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

use Google\Cloud\Storage\StorageClient;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

class GoogleCloudStorage implements CloudInterface
{
    /**
     * @var \Google\Cloud\Storage\Bucket $bucket
     */
    private $bucket;

    /**
     * GoogleCloudStorage constructor.
     * @param string $bucket
     * @param bool $userProject
     * @param array $config
     */
    public function __construct(array $config, string $bucket, $userProject = false)
    {
        try {
            $storage = new StorageClient($config);
            $this->bucket = $storage->bucket($bucket, $userProject);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(sprintf("Invalid inputs:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options = []): void
    {
        try {
            foreach (scandir($dir) as $filename) {
                $path = $dir . DIRECTORY_SEPARATOR . $filename;

                if (is_file($path)) {
                    $this->bucket->upload(fopen($path, 'r'), $options);
                }
            }
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf("There was an error during uploading files:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param string $save_to
     * @param array $options
     */
    public function download(string $save_to, array $options): void
    {
        $name = $options['filename'];
        unset($options['filename']);

        try {
            $this->bucket->object($name, $options)
                ->downloadToFile($save_to);
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf("There was an error during fetch the file:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }
}