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

use Streaming\FileManager;
use Streaming\HLS;
use Streaming\Utilities;

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
     * @param HLS $hls
     * @return array
     */
    private function HLSFilter(HLS $hls)
    {
        $filter = [];
        $representations = $hls->getRepresentations();
        $path_parts = $hls->getPathInfo();
        $dirname = str_replace("\\", "/", $path_parts["dirname"]);
        list($ts_sub_dir, $base_url) = $this->getSubDirectory($hls, $dirname);

        foreach ($representations as $key => $representation) {
            if ($key) {
                $filter = array_merge($filter, $this->getFormats($hls));
            }

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
            $filter[] = $hls->getHlsTime();
            $filter[] = "-hls_allow_cache";
            $filter[] = (int)$hls->isHlsAllowCache();
            $filter[] = "-b:v";
            $filter[] = $representation->getKiloBitrate() . "k";
            $filter[] = "-maxrate";
            $filter[] = intval($representation->getKiloBitrate() * 1.2) . "k";
            $filter[] = "-hls_segment_filename";
            $filter[] = $dirname . "/" . $ts_sub_dir . $path_parts["filename"] . "_" . $representation->getHeight() . "p_%04d.ts";
            $filter = array_merge($filter, $this->getBaseURL($base_url));
            $filter = array_merge($filter, $this->getKeyInfo($hls));
            $filter[] = "-strict";
            $filter[] = $hls->getStrict();

            if (end($representations) !== $representation) {
                $filter[] = $dirname . "/" . $path_parts["filename"] . "_" . $representation->getHeight() . "p.m3u8";
            }
        }

        return $filter;
    }

    /**
     * @param HLS $hls
     * @param $dirname
     * @return array
     */
    private function getSubDirectory(HLS $hls, $dirname)
    {
        $ts_sub_dir = Utilities::appendSlash($hls->getTsSubDirectory());
        $base_url = Utilities::appendSlash($hls->getHlsBaseUrl());

        if ($ts_sub_dir) {
            FileManager::makeDir($dirname . DIRECTORY_SEPARATOR . $ts_sub_dir);
            $base_url = $base_url . $hls->getTsSubDirectory() . "/";
        }

        return [$ts_sub_dir, $base_url];
    }

    private function getFormats(HLS $hls)
    {
        $format = ['-c:v', $hls->getFormat()->getVideoCodec()];

        $audio_format = $hls->getFormat()->getAudioCodec();
        if ($audio_format) {
            $format = array_merge($format, ['-c:a', $audio_format]);
        }

        return $format;
    }

    private function getBaseURL($base_url)
    {
        $filter = [];

        if ($base_url) {
            $filter[] = "-hls_base_url";
            $filter[] = $base_url;
        }

        return $filter;
    }

    private function getKeyInfo(HLS $hls)
    {
        $filter = [];

        if ($hls->getHlsKeyInfoFile()) {
            $filter[] = "-hls_key_info_file";
            $filter[] = $hls->getHlsKeyInfoFile();
        }

        return $filter;
    }
}