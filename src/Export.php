<?php

namespace AYazdanpanah\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\Traits\Formats;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var Filter */
    protected $filter;

    /**
     * Export constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @param string $path
     * @return Export
     */
    public function save(string $path = null): Export
    {
        $this->setFilter();

        $this->media->addFilter(
            $this->getFilter()
        );

        $this->media->save(
            $this->getFormat(),
            $this->getPath($path)
        );

        return $this;
    }

    /**
     * @return Filter
     */
    abstract protected function getFilter(): Filter;

    /**
     * @return mixed
     */
    abstract protected function setFilter(): void;

    private function getPath($path): string
    {
        if (null === $path) {
            if ($this instanceof DASH) {
                $path = $this->media->getPath() . '.mpd';
            } elseif ($this instanceof HLS) {
                $path = $this->media->getPath() . '.m3u8';
            }
        }

        return $path;
    }
}