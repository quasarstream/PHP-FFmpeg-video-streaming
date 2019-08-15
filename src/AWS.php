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


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use Streaming\Exception\Exception;

class AWS
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
     * @param $bucket
     * @param $key
     * @param $filename
     * @param string $acl
     * @return mixed
     * @throws Exception
     */
    public function uploadFile(string $bucket, string $key, string $filename, string $acl = 'public-read'): string
    {
        try {
            $result = $this->s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => fopen($filename, 'r'),
                'ACL' => $acl,
            ]);

            return isset($result['ObjectURL']) ? $result['ObjectURL'] : "It is private";
        } catch (S3Exception $e) {
            throw new Exception("There was an error uploading the file.\n error: " . $e->getMessage());
        }
    }

    /**
     * @param $bucket
     * @param $key
     * @param $filename
     * @throws Exception
     */
    public function downloadFile(string $bucket, string $key, string $filename): void
    {
        try {
            $file = $this->s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);

            if ($file['ContentLength'] > 0 && !empty($file['ContentType'])) {
                $body = $file->get('Body');
                file_put_contents($filename, $body);
            } else {
                throw new Exception("There is no file in the bucket");
            }
        } catch (S3Exception $e) {
            throw new Exception("There was an error downloading the file.\n error: " . $e->getMessage());
        }
    }

    /**
     * @param $source
     * @param $dest
     */
    public function uploadAndDownloadDirectory(string $source, string $dest): void
    {
        $manager = new Transfer($this->s3, $source, $dest);
        $manager->transfer();
    }
}