<?php

namespace AYazdanpanah\FFMpegStreaming;

class DASH extends Export
{

    /** @var string */
    protected $adaption;

    /**
     * @return mixed
     */
    public function getAdaption(): string
    {
        return $this->adaption;
    }

    /**
     * @param mixed $adaption
     * @return DASH
     */
    public function setAdaption(string $adaption): DASH
    {
        $this->adaption = $adaption;
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
