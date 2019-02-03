<?php

namespace AYazdanpanah\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\Traits\Representations;

class DASH extends Export
{
    use Representations;

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
    protected function setFilter(): void
    {
        $this->filter = new Filter($this);
    }
}
