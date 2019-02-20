<?php

/**
 * Copyright 2019 Amin Yazdanpanah<http://www.aminyazdanpanah.com>.
 *
 * Licensed under the MIT License;
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AYazdanpanah\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\Format\X264;
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
        $filter = $this->getDASHFilter();

        if($media->format instanceof X264){
            $filter[] = "-profile:v:0";
            $filter[] =  "main";
        }

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
                if ($key > 0 && $media->format instanceof X264) {
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
            "-f",
            "hls"
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

    private function getDASHFilter()
    {
        return [
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
    }
}