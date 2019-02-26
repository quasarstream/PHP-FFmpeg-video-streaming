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


namespace AYazdanpanah\FFMpegStreaming\Filters;


use AYazdanpanah\FFMpegStreaming\DASH;
use AYazdanpanah\FFMpegStreaming\Format\X264;
use AYazdanpanah\FFMpegStreaming\Representation;

class DASHFilter extends Filter
{
    /**
     * @param $media
     */
    public function setFilter($media): void
    {
        $this->filter = $this->DASHFilter($media);
    }

    /**
     * @param DASH $media
     * @return array
     */
    private function DASHFilter(DASH $media)
    {
        $filter = $this->getAdditionalFilters($media->getFormat(), count($media->getRepresentations()));

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
            }
        }

        if ($media->getAdaption()) {
            $filter[] = "-adaptation_sets";
            $filter[] = $media->getAdaption();
        }

        return $filter;
    }

    /**
     * @param $format
     * @param $count
     * @return array
     */
    private function getAdditionalFilters($format, $count)
    {
        $filter = [
            "-bf", "1", "-keyint_min", "120", "-g", "120",
            "-sc_threshold", "0", "-b_strategy", "0",
            "-use_timeline", "1", "-use_template", "1", "-f", "dash"
        ];

        if ($format instanceof X264) {
            $filter[] = "-profile:v:0";
            $filter[] = "main";

            while ($count > 0) {
                $filter[] = "-profile:v:" . $count;
                $filter[] = "baseline";
                $count--;
            }
        }

        return $filter;
    }
}