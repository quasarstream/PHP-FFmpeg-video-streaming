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

class S3 implements CloudInterface
{
    /** @var \Aws\S3\S3Client*/
    private $s3;

    /**
     * AWS constructor.
     * @param  $config
     */
    public function __construct(array $config)
    {
        if(!class_exists('\Aws\S3\S3Client')){
            throw new RuntimeException('Aws\S3\S3Client not found. make sure the package is installed: composer require aws/aws-sdk-php');
        }

        $this->s3 = new \Aws\S3\S3Client($config);;
    }

    /**
     * @param  string $dir
     * @param  array $options
     */
    public function uploadDirectory(string $dir, array $options): void
    {
        if(!isset($options['dest'])){
            throw new InvalidArgumentException("You should set the dest in the array");
        }
        $dest = $options['dest'];
        unset($options['dest'], $options['filename']);

        try {
            (new \Aws\S3\Transfer($this->s3, $dir, $dest, $options))->transfer();
        } catch (\Aws\S3\Exception\S3Exception $e) {
            throw new RuntimeException("An error occurred while uploading files: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param  string $save_to
     * @param  array $options
     * @throws  RuntimeException
     */
    public function download(string $save_to, array $options): void
    {
        try {
            $file = $this->s3->getObject($options);
            if ($file['ContentLength'] > 0 && !empty($file['ContentType'])) {
                File::put($save_to, $file->get('Body'));
            } else {
                throw new RuntimeException("File not found!");
            }
        } catch (\Aws\S3\Exception\S3Exception $e) {
            throw new RuntimeException("An error occurred while downloading the file: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}