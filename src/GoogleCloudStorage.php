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
        $storage = new StorageClient($config);
        $this->bucket = $storage->bucket($bucket, $userProject);
    }

    /**
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options = [])
    {
        foreach (scandir($dir) as $key => $filename) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;

            if (is_file($path)) {
                $this->bucket->upload(fopen($path, 'r'), $options);
            }
        }
    }

    public function download(string $name, string $save_to)
    {
        return $this->bucket->object($name)
            ->downloadToFile($save_to);
    }
}