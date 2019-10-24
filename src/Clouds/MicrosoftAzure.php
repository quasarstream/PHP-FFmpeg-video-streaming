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


use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use Streaming\Exception\RuntimeException;

class MicrosoftAzure implements CloudInterface
{
    private $blobClient;

    /**
     * MicrosoftAzure constructor.
     * @param $connectionString
     */
    public function __construct($connectionString)
    {
        @trigger_error(
            'MicrosoftAzure class is deprecated and will be removed in a future release. Use CloudInterface instead.
            For more information see https://video.aminyazdanpanah.com/start/open-clouds and https://video.aminyazdanpanah.com/start/save-clouds',
            E_USER_DEPRECATED
        );

        $this->blobClient = BlobRestProxy::createBlobService($connectionString);
    }

    /**
     * Upload a entire directory to a cloud
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        $container = $options['container'];

        try {
            foreach (scandir($dir) as $filename) {
                $path = $dir . DIRECTORY_SEPARATOR . $filename;

                if (is_file($path)) {
                    $this->blobClient->createBlockBlob($container, $filename, fopen($path, "r"));
                }
            }
        } catch (ServiceException $e) {
            throw new RuntimeException(sprintf("There was an error during uploading files:\n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * Download a file from a cloud
     * @param string $save_to
     * @param array $options
     */
    public function download(string $save_to, array $options): void
    {
        $container = $options['container'];
        $blob = $options['blob'];

        try {
            $getBlobResult = $this->blobClient->getBlob($container, $blob);
        } catch (ServiceException $e) {
            throw new RuntimeException(sprintf("There was an error during uploading files:\n %s", $e->getMessage()), $e->getCode(), $e);
        }

        file_put_contents($save_to, $getBlobResult->getContentStream());
    }
}