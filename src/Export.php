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
use Streaming\Clouds\CloudManager;
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
     * @return bool
     */
    public function isTmpDir(): bool
    {
        return (bool)$this->tmp_dir;
    }

    /**
     * @return array
     */
    public function getPathInfo(): array
    {
        return $this->path_info;
    }

    /**
     * @param string|null $path
     */
    private function moveTmpFolder(?string $path): void
    {
        if ($this->tmp_dir && $path) {
            FileManager::moveDir($this->tmp_dir, pathinfo($path, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR);
            $this->path_info = pathinfo($path);
        }
    }

    /**
     * @return string
     */
    private function getPath(): string
    {
        $path = substr(str_replace("\\", "/", $this->path_info["dirname"] . "/" . $this->path_info["filename"]), 0, PHP_MAXPATHLEN);

        if ($this instanceof DASH) {
            $path = $path . ".mpd";
        } elseif ($this instanceof HLS) {
            ExportHLSPlaylist::savePlayList($path . ".m3u8", $this->getRepresentations(), $this->path_info["filename"]);

            $representations = $this->getRepresentations();
            $path = $path . "_" . end($representations)->getHeight() . "p.m3u8";
        }

        return $path;
    }

    /**
     * @return Filter
     */
    abstract protected function getFilter(): Filter;

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
            throw new RuntimeException(sprintf("There was an error saving files: \n\n reason: \n %s", $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @param $path
     */
    private function tmpDirectory($path): void
    {
        $basename = $path ? pathinfo($path, PATHINFO_BASENAME) : Utilities::randomString();

        $this->tmp_dir = FileManager::tmpDir();
        $this->path_info = pathinfo($this->tmp_dir . $basename);
    }

    /**
     * @param $path
     * @param $clouds
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
     * @param string $path
     * @param array $clouds
     * @param bool $metadata
     * @return mixed
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
         * 5. Return all video and also streams' metadata and save as a json file on the local machine(it won't save metadata to clouds because of some security reasons).
         * 6. In the end, clear all tmp files.
         * ------------------------------------------------------------------------------
         */

        $this->createPathInfoAndTmpDir($path, $clouds);
        $this->runFFmpeg();
        CloudManager::uploadDirectory($clouds, $this->tmp_dir);
        $this->moveTmpFolder($path);

        return $metadata ? (new Metadata($this))->extract() : $this;
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
}