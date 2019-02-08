<?php

namespace AYazdanpanah\FFMpegStreaming;

class HLS extends Export
{

    /** @var string */
    protected $stream_map;

    /**
     * @return mixed
     */
    public function getStreamMap(): string
    {
        return $this->stream_map;
    }

    /**
     * @param mixed $adaption
     * @return HLS
     */
    public function setStreamMap(string $adaption): HLS
    {
        $this->stream_map = $adaption;
        return $this;
    }

    /**
     * @return Filter
     */
    protected function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return mixed|void
     */
    protected function setFilter()
    {
        $this->filter = new Filter($this);
    }
}
