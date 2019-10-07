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


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use Streaming\Exception\Exception;
use Streaming\Exception\RuntimeException;

class AWS implements CloudInterface
{
    private $s3;

    /**
     * AWS constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->s3 = new S3Client($config);;
    }

    /**
     * @param string $save_to
     * @param array $options
     * @throws Exception
     */
    public function download(string $save_to, array $options): void
    {
        $bucket = $options['bucket'];
        $key = $options['key'];

        try {
            $file = $this->s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);

            if ($file['ContentLength'] > 0 && !empty($file['ContentType'])) {
                $body = $file->get('Body');
                file_put_contents($save_to, $body);
            } else {
                throw new Exception("There is no file in the bucket");
            }
        } catch (S3Exception $e) {
            throw new RuntimeException("There was an error downloading the file.\n error: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $dir
     * @param array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        $dest = $options['dest'];

        try {
            $manager = new Transfer($this->s3, $dir, $dest);
            $manager->transfer();
        } catch (S3Exception $e) {
            throw new RuntimeException("There was an error downloading the file.\n error: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}