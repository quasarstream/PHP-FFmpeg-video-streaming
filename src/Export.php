<?php

namespace AYazdanpanah\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\Exception\Exception;
use AYazdanpanah\FFMpegStreaming\Traits\Formats;
use FFMpeg\Format\FormatInterface;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var object */
    protected $format;

    /** @var Filter */
    protected $filter;

    /** @var array */
    protected $representations = [];

    /**
     * @param Representation $representation
     * @return $this
     * @throws Exception
     */
    public function addRepresentation(Representation $representation): Export
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations[] = $representation;
        return $this;
    }

    /**
     * Export constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @return FormatInterface|mixed
     */
    private function getFormat(): FormatInterface
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return Export
     */
    protected function setFormat($format): Export
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $path
     * @return Export
     */
    public function save(string $path): Export
    {
        $this->setFilter();

        $this->media->addFilter(
            $this->getFilter()
        );

        $this->media->save(
            $this->getFormat(),
            $path
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
    abstract protected function setFilter();

}
