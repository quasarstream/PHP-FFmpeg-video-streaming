<?php

namespace App\Dash;


use FFMpeg\Filters\FilterInterface;

class Filter implements FilterInterface
{
    private $priority = 2;

    private $filter = [];

    /**
     * Filter constructor.
     * @param Export $media
     */
    public function __construct(Export $media)
    {
        $this->setFilter($media);
    }


    /**
     * Applies the filter on the the Audio media given an format.
     *
     * @return array An array of arguments
     */
    public function apply(): array
    {
        return $this->getFilter();
    }

    /**
     * Returns the priority of the filter.
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param $media
     */
    public function setFilter(Export $media): void
    {
        if ($media instanceof DASH) {
            $this->filter = $this->DASHFilter($media);
        } elseif ($media instanceof HLS) {
            $this->filter = $this->HLSFilter($media);
        } elseif ($media instanceof Live) {
            $this->filter = $this->liveFilter($media);
        }
    }

    /**
     * @param DASH $media
     * @return array
     */
    private function DASHFilter(DASH $media)
    {
        $filter = [
            "-profile:v:0",
            "main",
            "-bf",
            "1",
            "-keyint_min",
            "120",
            "-g",
            "120",
            "-sc_threshold",
            "0",
            "-b_strategy",
            "0",
            "-use_timeline",
            "1",
            "-use_template",
            "1",
            "-f",
            "dash"
        ];

        foreach ($media->getRepresentations() as $key => $representation) {
            if ($representation instanceof Representation) {
                $filter[] = "-map";
                $filter[] = "0";
                $filter[] = "-b:v:" . $key;
                $filter[] = $representation->getKiloBitrate() . "k";
                if (null !== $representation->getResize()) {
                    $filter[] = "-s:v:" . $key;
                    $filter[] = $representation->getResize();
                }
                if ($key > 0) {
                    $filter[] = "-profile:v:" . $key;
                    $filter[] = "baseline";
                }
            }
        }

        if ($media->getAdaption()) {
            $filter[] = "-adaptation_sets";
            $filter[] = $media->getAdaption();
        }

        return $filter;
    }

    /**
     * @param HLS $media
     * @return array
     */
    private function HLSFilter(HLS $media)
    {
        $filter = [
            "-hls_segment_filename",
            "'file_%v_%03d.ts'"
        ];

        foreach ($media->getRepresentations() as $key => $representation) {
            if ($representation instanceof Representation) {
                $filter[] = "-map";
                $filter[] = "0:v";
                $filter[] = "-b:v:" . $key;
                $filter[] = $representation->getKiloBitrate() . "k";
                if (null !== $representation->getResize()) {
                    $filter[] = "-s:v:" . $key;
                    $filter[] = $representation->getResize();
                }
            }
        }

        if ($media->getStreamMap()) {
            $filter[] = "-var_stream_map";
            $filter[] = $media->getStreamMap();
        }

        return $filter;
    }

    /**
     * @param Live $media
     */
    private function liveFilter(Live $media)
    {
        die("Live (Soon)");
    }
}