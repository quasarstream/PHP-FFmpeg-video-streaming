<?php

/**
 * This file is part of the ******* package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming;

use Google\Cloud\Storage\StorageClient;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

class GoogleCloudStorage
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
        try{
            $storage = new StorageClient($config);
            $this->bucket = $storage->bucket($bucket, $userProject);
        }catch (\Exception $e){
            throw new InvalidArgumentException(sprintf("Invalid inputs:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options = [])
    {
        try{
            foreach (scandir($dir) as $key => $filename) {
                $path = $dir . DIRECTORY_SEPARATOR . $filename;

                if (is_file($path)) {
                    $this->bucket->upload(fopen($path, 'r'), $options);
                }
            }
        }catch (\Exception $e){
            throw new RuntimeException(sprintf("There wan an error during uploading files:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @param string $save_to
     * @return \Psr\Http\Message\StreamInterface
     */
    public function download(string $name, string $save_to)
    {
        try{
            return $this->bucket->object($name)
                ->downloadToFile($save_to);
        }catch (\Exception $e){
            throw new RuntimeException(sprintf("There wan an error during fetch the file:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }
}