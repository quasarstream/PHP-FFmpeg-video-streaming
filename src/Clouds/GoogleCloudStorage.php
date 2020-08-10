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

use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

class GoogleCloudStorage implements CloudInterface
{
    /** @var \Google\Cloud\Storage\StorageClient*/
    private $storage;
    /**
     * GoogleCloudStorage constructor.
     * @param  array $config
     */
    public function __construct(array $config)
    {
        if(!class_exists('\Google\Cloud\Storage\StorageClient')){
            throw new RuntimeException('Google\Cloud\Storage\StorageClient not found. make sure the package is installed: composer require google/cloud-storage');
        }

        try {
            $this->storage = new \Google\Cloud\Storage\StorageClient($config);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(sprintf("Invalid inputs:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param  string $dir
     * @param  array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        $bucket = $this->getBucket($options);
        $cloud_filename = dirname($options['filename'] ?? '');

        unset($options['bucket'], $options['user_project'], $options['filename']);

        try {
            foreach (scandir($dir) as $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                $name = $cloud_filename ? implode('/', [$cloud_filename, $file]) : $file;
                $options = array_merge($options, ['name' => $name]);

                if (is_file($path)) {
                    $bucket->upload(fopen($path, 'r'), $options);
                }
            }
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf("An error occurred while uploading files: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param  string $save_to
     * @param  array $options
     */
    public function download(string $save_to, array $options): void
    {
        $bucket = $this->getBucket($options);

        if(!isset($options['bucket'])){
            throw new InvalidArgumentException("You need to set the bucket name in the option array");
        }
        $name = $options['object_name'];
        unset($options['bucket'], $options['user_project'], $options['object_name']);

        try {
            $bucket->object($name, $options)->downloadToFile($save_to);
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf("An error occurred while downloading the file: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param array $options
     * @return \Google\Cloud\Storage\Bucket
     */
    private function getBucket(array $options): \Google\Cloud\Storage\Bucket
    {
        if(!isset($options['bucket'])){
            throw new InvalidArgumentException("You need to set the bucket name in the option array");
        }
        try{
            return $this->storage->bucket($options['bucket'], $options['user_project'] ?? false);
        }catch (\Exception $e){
            throw new RuntimeException(sprintf("An error occurred while opening the bucket: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }
}