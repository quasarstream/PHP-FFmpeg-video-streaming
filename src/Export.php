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
use Streaming\Clouds\AWS;
use Streaming\Clouds\Cloud;
use Streaming\Clouds\CloudManager;
use Streaming\Clouds\GoogleCloudStorage;
use Streaming\Clouds\MicrosoftAzure;
use Streaming\Exception\Exception;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;
use Streaming\Filters\Filter;
use Streaming\Traits\Formats;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var array */
    protected $path_info;

    /** @var string */
    protected $strict = "-2";

    /** @var string */
    protected $tmp_dir;

    /**
     * Export constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->path_info = pathinfo($media->getPath());
    }

    /**
     * @param string $path
     * @param array $clouds
     * @param bool $metadata
     * @return mixed
     * @throws Exception
     */
    public function save(string $path = null, array $clouds = [], bool $metadata = true)
    {
        /**
         * Synopsis
         * ------------------------------------------------------------------------------
         * 1. Create directory path, path info array, and temporary folders(if it is required).
         * 2. Build object and run FFmpeg to package media content and save on the local machine.
         * 3. If the cloud is specified, entire packaged files will be uploaded to clouds.
         * 4. If files were saved into a tmp folder, then they will be moved to the local path(if the path is specified).
         * 5. Return all video and also streams' metadata and save as a json file on the local machine(it won't save metadata to clouds because of some security concerns).
         * 6. In the end, clear all tmp files.
         * ------------------------------------------------------------------------------
         */

        $this->createPathInfoAndTmpDir($path, $clouds);
        $this->runFFmpeg();
        CloudManager::saveToClouds($clouds, $this->tmp_dir);
        $this->moveTmpFolder($path);

        return $metadata ? (new Metadata($this))->extract() : $this;
    }

    /**
     * @param $path
     * @param $clouds
     * @throws Exception
     */
    private function createPathInfoAndTmpDir($path, $clouds): void
    {
        if (null !== $path) {
            $this->path_info = pathinfo($path);
            FileManager::makeDir($this->path_info["dirname"]);
        }

        if ($clouds) {
            $this->tmpDirectory($path);
        }

        if (null === $path && $this->media->isTmp() && !$clouds) {
            throw new InvalidArgumentException("You need to specify a path. It is not possible to save to a tmp directory");
        }
    }

    /**
     * @param $path
     * @throws Exception
     */
    private function tmpDirectory($path)
    {
        if (null !== $path) {
            $basename = pathinfo($path, PATHINFO_BASENAME);
        } else {
            $basename = Helper::randomString();
        }

        $this->tmp_dir = FileManager::tmpDir();
        $this->path_info = pathinfo($this->tmp_dir . $basename);
    }

    /**
     * Run FFmpeg to package media content
     */
    private function runFFmpeg(): void
    {
        try {
            $this->media
                ->addFilter($this->getFilter())
                ->save($this->getFormat(), $this->getPath());
        } catch (ExceptionInterface $e) {
            throw new RuntimeException(sprintf("There was an error saving files: \n\n reason: \n %s", $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return Filter
     */
    abstract protected function getFilter(): Filter;

    /**
     * @return string
     */
    private function getPath(): string
    {
        $dirname = str_replace("\\", "/", $this->path_info["dirname"]);
        $filename = substr($this->path_info["filename"], -100);
        $path = '';

        if ($this instanceof DASH) {
            $path = $dirname . "/" . $filename . ".mpd";
        } elseif ($this instanceof HLS) {
            $representations = $this->getRepresentations();
            $path = $dirname . "/" . $filename . "_" . end($representations)->getHeight() . "p.m3u8";
            ExportHLSPlaylist::savePlayList($dirname . DIRECTORY_SEPARATOR . $filename . ".m3u8", $this->getRepresentations(), $filename);
        }

        return $path;
    }

    /**
     * @param string|null $path
     * @throws Exception
     */
    private function moveTmpFolder(?string $path)
    {
        if ($this->tmp_dir && $path) {
            FileManager::moveDir($this->tmp_dir, pathinfo($path, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR);
            $this->path_info = pathinfo($path);
        }
    }

    /**
     * @return array
     */
    public function getPathInfo(): array
    {
        return $this->path_info;
    }

    /**
     * @return object|Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @param string $strict
     * @return Export
     */
    public function setStrict(string $strict): Export
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrict(): string
    {
        return $this->strict;
    }

    /**
     * clear tmp files
     */
    public function __destruct()
    {
        sleep(1);

        if ($this->media->isTmp()) {
            @unlink($this->media->getPath());
        }

        if ($this->tmp_dir) {
            FileManager::deleteDirectory($this->tmp_dir);
        }

        if ($this instanceof HLS && $this->tmp_key_info_file) {
            @unlink($this->getHlsKeyInfoFile());
        }
    }

    /**
     * @param string $url
     * @param string $name
     * @param string|null $path
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return mixed
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function saveToCloud(
        string $url,
        string $name,
        string $path = null,
        string $method = 'GET',
        array $headers = [],
        array $options = []
    )
    {
        @trigger_error('saveToCloud method is deprecated and will be removed in a future release. Use Cloud instead', E_USER_DEPRECATED);
        if ($this instanceof HLS && $this->getTsSubDirectory()) {
            throw new InvalidArgumentException("It is not possible to create subdirectory in a cloud");
        }
        $results = $this->saveToTemporaryFolder($path);
        sleep(1);

        $cloud = new Cloud($url, $method, $options);
        $cloud->uploadDirectory($this->tmp_dir, ['name' => $name, 'headers' => $headers]);

        $this->moveTmpFolder($path);

        return $results;
    }

    /**
     * @param array $config
     * @param string $dest
     * @param string|null $path
     * @return mixed
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function saveToS3(
        array $config,
        string $dest,
        string $path = null
    )
    {
        @trigger_error('saveToS3 method is deprecated and will be removed in a future release. Use AWS instead', E_USER_DEPRECATED);
        $results = $this->saveToTemporaryFolder($path);
        sleep(1);

        $aws = new AWS($config);
        $aws->uploadDirectory($this->tmp_dir, ['dest' => $dest]);

        $this->moveTmpFolder($path);

        return $results;
    }

    /**
     * @param array $config
     * @param string $bucket
     * @param string|null $path
     * @param array $options
     * @param bool $userProject
     * @return mixed
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function saveToGCS(
        array $config,
        string $bucket,
        string $path = null,
        array $options = [],
        bool $userProject = false
    )
    {
        @trigger_error('saveToGCS method is deprecated and will be removed in a future release. Use GoogleCloudStorage instead', E_USER_DEPRECATED);
        if ($this instanceof HLS && $this->getTsSubDirectory()) {
            throw new InvalidArgumentException("It is not possible to create subdirectory in a cloud");
        }

        $results = $this->saveToTemporaryFolder($path);
        sleep(1);

        $google_cloud = new GoogleCloudStorage($config, $bucket, $userProject);
        $google_cloud->uploadDirectory($this->tmp_dir, $options);

        $this->moveTmpFolder($path);

        return $results;
    }

    /**
     * @param string $connectionString
     * @param string $container
     * @param string|null $path
     * @return mixed
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    public function saveToMAS(
        string $connectionString,
        string $container,
        string $path = null
    )
    {
        @trigger_error('saveToMAS method is deprecated and will be removed in a future release. Use MicrosoftAzure instead', E_USER_DEPRECATED);

        if ($this instanceof HLS && $this->getTsSubDirectory()) {
            throw new InvalidArgumentException("It is not possible to create subdirectory in a cloud");
        }

        $results = $this->saveToTemporaryFolder($path);
        sleep(1);

        $google_cloud = new MicrosoftAzure($connectionString);
        $google_cloud->uploadDirectory($this->tmp_dir, ['container' => $container]);

        $this->moveTmpFolder($path);

        return $results;
    }

    /**
     * @param $path
     * @return array
     * @throws Exception
     * @deprecated this method is deprecated
     */
    // @TODO: should be removed in the next releases.
    private function saveToTemporaryFolder($path)
    {
        $basename = Helper::randomString();

        if (null !== $path) {
            $basename = pathinfo($path, PATHINFO_BASENAME);
        }

        $this->tmp_dir = FileManager::tmpDir();

        return $this->save($this->tmp_dir . $basename);
    }
}