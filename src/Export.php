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

use Streaming\Filters\Filter;
use Streaming\Traits\Formats;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var Filter */
    protected $filter;

    /** @var array */
    protected $path_info;

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
     * @param bool $analyse
     * @param bool $delete_original_video
     * @return mixed
     */
    public function save(string $path = null, $analyse = true, $delete_original_video = false)
    {
        $path = $this->getPath($path);

        $this->setFilter();

        $this->media->addFilter(
            $this->getFilter()
        );

        $this->media->save(
            $this->getFormat(),
            $path
        );

        if ($delete_original_video) {
            sleep(1);
            @unlink($this->media->getPath());
        }

        return ($analyse) ? (new StreamingAnalytics($this))->analyse() : $path;
    }

    /**
     * @return Filter
     */
    abstract protected function getFilter(): Filter;

    /**
     * @return mixed
     */
    abstract protected function setFilter();

    private function getPath($path): string
    {
        if (null !== $path) {
            $this->path_info = pathinfo($path);
        }

        $dirname = str_replace("\\", "/", $this->path_info["dirname"]);
        $filename = substr($this->path_info["filename"], -50);

        Helper::makeDir($dirname);

        if ($this instanceof DASH) {
            $path = $dirname . "/" . $filename . ".mpd";
        } elseif ($this instanceof HLS) {
            $representations = $this->getRepresentations();
            $path = $dirname . "/" . $filename . "_" . end($representations)->getHeight() . "p.m3u8";
            ExportHLSPlaylist::savePlayList($dirname . "/" . $filename . ".m3u8", $this->getRepresentations(), $filename);
        }

        return $path;
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
    public function getMedia()
    {
        return $this->media;
    }
}
