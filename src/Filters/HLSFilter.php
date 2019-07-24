<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming\Filters;

use Streaming\HLS;
use Streaming\Representation;

class HLSFilter extends Filter
{
    /**
     * @param $media
     * @return mixed|void
     */
    public function setFilter($media): void
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

        foreach ($representations as $representation) {
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

                $filter[] = "-strict";
                $filter[] = "-2";

                if (++$counter !== $total_count) {
                    $filter[] = $dirname . "/" . $filename . "_" . $representation->getHeight() . "p.m3u8";
                }
            }
        }
        return $filter;
    }
}