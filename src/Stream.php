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
use Streaming\Clouds\Cloud;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;
use Streaming\Filters\StreamFilterInterface;
use Streaming\Traits\Formats;


abstract class Stream implements StreamInterface
{
    use Formats;

    /** @var Media */
    private $media;

    /** @var string */
    protected $path;

    /** @var string */
    private $tmp_dir = '';

    /**
     * Stream constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->path = $media->getPathfile();
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @return bool
     */
    public function isTmpDir(): bool
    {
        return (bool)$this->tmp_dir;
    }

    /**
     * @param int $option
     * @return string
     */
    public function pathInfo(int $option): string
    {
        return pathinfo($this->path, $option);
    }

    /**
     * @param string|null $path
     */
    private function moveTmp(?string $path): void
    {
        if ($this->isTmpDir() && !is_null($path)) {
            File::move($this->tmp_dir, dirname($path));
            $this->path = $path;
            $this->tmp_dir = '';
        }
    }

    /**
     * @param array $clouds
     * @param string $path
     */
    private function clouds(array $clouds, ?string $path): void
    {
        if (!empty($clouds)) {
            Cloud::uploadDirectory($clouds, $this->tmp_dir);
            $this->moveTmp($path);
        }
    }

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        return str_replace(
            "\\",
            "/",
            $this->pathInfo(PATHINFO_DIRNAME) . "/" . $this->pathInfo(PATHINFO_FILENAME)
        );
    }

    /**
     * @return string
     */
    abstract protected function getPath(): string;

    /**
     * @return StreamFilterInterface
     */
    abstract protected function getFilter(): StreamFilterInterface;

    /**
     * Run FFmpeg to package media content
     */
    private function run(): void
    {
        $this->media->addFilter($this->getFilter());

        $commands = (new CommandBuilder($this->media, $this->getFormat()))->build($this->getFormat(), $this->getPath());
        $pass = $this->format->getPasses();
        $listeners = $this->format->createProgressListener($this->media->baseMedia(), $this->media->getFFProbe(), 1, $pass);

        try {
            $this->media->getFFMpegDriver()->command($commands, false, $listeners);
        } catch (ExceptionInterface $e) {
            throw new RuntimeException("An error occurred while saving files: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $path
     * @param $clouds
     */
    private function paths(?string $path, array $clouds): void
    {
        if (!empty($clouds)) {
            $this->tmp_dir = File::tmpDir();
            $this->path = $this->tmp_dir . basename($clouds['options']['filename'] ?? $path ?? $this->path);
        } elseif (!is_null($path)) {
            if (strlen($path) > PHP_MAXPATHLEN) {
                throw new InvalidArgumentException("The path is too long");
            }

            File::makeDir(dirname($path));
            $this->path = $path;
        } elseif ($this->media->isTmp()) {
            throw new InvalidArgumentException("You need to specify a path. It is not possible to save to a tmp directory");
        }
    }

    /**
     * @param string $path
     * @param array $clouds
     * @return mixed
     */
    public function save(string $path = null, array $clouds = []): Stream
    {
        $this->paths($path, $clouds);
        $this->run();
        $this->clouds($clouds, $path);

        return $this;
    }

    /**
     * @param string $url
     */
    public function live(string $url): void
    {
        $this->path = $url;
        $this->run();
    }

    /**
     * @return Metadata
     */
    public function metadata(): Metadata
    {
        return new Metadata($this);
    }

    /**
     * clear tmp files
     */
    public function __destruct()
    {
        // make sure that FFmpeg process has benn terminated
        sleep(.5);
        File::remove($this->tmp_dir);

        if ($this->media->isTmp()) {
            File::remove($this->media->getPathfile());
        }
    }
}