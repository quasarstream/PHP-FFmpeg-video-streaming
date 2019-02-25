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


use AYazdanpanah\FFMpegStreaming\HLS;
use AYazdanpanah\FFMpegStreaming\Representation;

class HLSFilter extends Filter
{

    public function setFilter($media)
    {
        $this->filter = $this->HLSFilter($media);
    }

    /**
     * @param HLS $media
     * @return array
     */
    private function HLSFilter(HLS $media)
    {

        $filter = [];
        $total_count = count($representations = $media->getRepresentations());
        $counter = 0;
        $path_parts = $media->getPathInfo();
        $dirname = str_replace("\\", "/", $path_parts["dirname"]);
        $filename = substr($path_parts["filename"], -50);

        foreach ($representations as $key => $representation) {
            if ($representation instanceof Representation) {
                $filter[] = "-s:v";
                $filter[] = $representation->getResize();
                $filter[] = "-crf";
                $filter[] = "20";
                $filter[] = "-sc_threshold";
                $filter[] = "0";
                $filter[] = "-g";
                $filter[] = "48";
                $filter[] = "-keyint_min";
                $filter[] = "48";
                $filter[] = "-hls_list_size";
                $filter[] = "0";
                $filter[] = "-hls_time";
                $filter[] = $media->getHlsTime();
                $filter[] = "-hls_allow_cache";
                $filter[] = $media->isHlsAllowCache() ? "1" : "0";
                $filter[] = "-b:v";
                $filter[] = $representation->getKiloBitrate() . "k";
                $filter[] = "-maxrate";
                $filter[] = intval($representation->getKiloBitrate() * 1.2) . "k";
                $filter[] = "-hls_segment_filename";
                $filter[] = $dirname . "/" . $filename . "_" . $representation->getHeight() . "p_%04d.ts";

                if (($hls_key_info_file = $media->getHlsKeyInfoFile()) !== "") {
                    $filter[] = "-hls_key_info_file";
                    $filter[] = $hls_key_info_file;
                }

                if (++$counter !== $total_count) {
                    $filter[] = $dirname . "/" . $filename . "_" . $representation->getHeight() . "p.m3u8";
                }
            }
        }

        return $filter;
    }
}