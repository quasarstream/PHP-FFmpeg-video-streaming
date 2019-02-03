<?php

namespace AYazdanpanah\FFMpegStreaming\Traits;


use AYazdanpanah\FFMpegStreaming\AutoRepresentations;
use AYazdanpanah\FFMpegStreaming\Exception\Exception;
use AYazdanpanah\FFMpegStreaming\Representation;

trait Representations
{
    /** @var array */
    protected $representations = [];

    /**
     * @param Representation $representation
     * @return $this
     * @throws Exception
     */
    public function addRepresentation(Representation $representation)
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations[] = $representation;
        return $this;
    }

    /**
     * @return array
     */
    public function getRepresentations(): array
    {
        return $this->representations;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function autoGenerateRepresentations()
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations = (new AutoRepresentations($this->media->getFirstStream()))
            ->get();

        return $this;
    }

}