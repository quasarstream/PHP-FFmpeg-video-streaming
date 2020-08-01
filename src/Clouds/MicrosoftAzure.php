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
use Streaming\File;

class MicrosoftAzure implements CloudInterface
{
    /** @var \MicrosoftAzure\Storage\Blob\BlobRestProxy*/
    private $blobClient;

    /**
     * MicrosoftAzure constructor.
     * @param  $connection
     * @param array $options
     */
    public function __construct($connection, array $options = [])
    {
        if(!class_exists('\MicrosoftAzure\Storage\Blob\BlobRestProxy')){
            throw new RuntimeException('MicrosoftAzure\Storage\Blob\BlobRestProxy not found. make sure the package is installed: composer require microsoft/azure-storage-blob');
        }

        $this->blobClient = \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobService($connection, $options);
    }

    /**
     * Upload a entire directory to a cloud
     * @param  string $dir
     * @param  array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        if(!isset($options['container'])){
            throw new InvalidArgumentException("You should set the container in the array");
        }

        unset($options['filename']);

        try {
            foreach (scandir($dir) as $filename) {
                $path = $dir . DIRECTORY_SEPARATOR . $filename;

                if (is_file($path)) {
                    $this->blobClient->createBlockBlob($options['container'], $filename, fopen($path, "r"), $options['CreateBlockBlobOptions'] ?? null);
                }
            }
        } catch (\MicrosoftAzure\Storage\Common\Exceptions\ServiceException $e) {
            throw new RuntimeException(sprintf("An error occurred while uploading files: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * Download a file from a cloud
     * @param  string $save_to
     * @param  array $options
     */
    public function download(string $save_to, array $options): void
    {
        if(!isset($options['container']) ||  !isset($options['blob'])){
            throw new InvalidArgumentException("You should set the container and blob in the array");
        }

        try {
            $blob = $this->blobClient->getBlob($options['container'], $options['blob']);
            File::put($save_to, $blob->getContentStream());
        } catch (\MicrosoftAzure\Storage\Common\Exceptions\ServiceException $e) {
            throw new RuntimeException(sprintf("An error occurred while downloading the file: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }
}