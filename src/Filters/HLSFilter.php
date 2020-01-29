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

use Streaming\File;
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
     * @param HLS $hls
     * @return array
     */
    private function HLSFilter(HLS $hls): array
    {
        $filter = [];
        $reps = $hls->getRepresentations();
        $path_parts = $hls->getPathInfo();
        $dirname = str_replace("\\", "/", $path_parts["dirname"]);
        list($ts_sub_dir, $base_url) = $this->getSubDirectory($hls, $dirname);

        foreach ($reps as $key => $rep) {
            if ($key) {
                $filter = array_merge($filter, $this->getFormats($hls));
            }

            $filter[] = "-s:v";
            $filter[] = $rep->getResize();
            $filter[] = "-crf";
            $filter[] = "20";
            $filter[] = "-sc_threshold";
            $filter[] = "0";
            $filter[] = "-g";
            $filter[] = "48";
            $filter[] = "-keyint_min";
            $filter[] = "48";
            $filter[] = "-hls_list_size";
            $filter[] = $hls->getHlsListSize();
            $filter[] = "-hls_time";
            $filter[] = $hls->getHlsTime();
            $filter[] = "-hls_allow_cache";
            $filter[] = (int)$hls->isHlsAllowCache();
            $filter[] = "-b:v";
            $filter[] = $rep->getKiloBitrate() . "k";
            $filter = array_merge($filter, $this->getAudioBitrate($rep));
            $filter[] = "-maxrate";
            $filter[] = intval($rep->getKiloBitrate() * 1.2) . "k";
            $filter[] = "-hls_segment_filename";
            $filter[] = $dirname . "/" . $ts_sub_dir . $path_parts["filename"] . "_" . $rep->getHeight() . "p_%04d.ts";
            $filter = array_merge($filter, $this->getBaseURL($base_url));
            $filter = array_merge($filter, $this->getKeyInfo($hls));
            $filter = array_merge($filter, $hls->getAdditionalParams());
            $filter[] = "-strict";
            $filter[] = $hls->getStrict();

            if (end($reps) !== $rep) {
                $filter[] = $dirname . "/" . $path_parts["filename"] . "_" . $rep->getHeight() . "p.m3u8";
            }
        }

        return $filter;
    }

    /**
     * @param HLS $hls
     * @param $dirname
     * @return array
     */
    private function getSubDirectory(HLS $hls, string $dirname): array
    {
        if ($hls->getTsSubDirectory()) {
            File::makeDir($dirname . "/" . $hls->getTsSubDirectory() . "/");
        }

        $base = $hls->getHlsBaseUrl() ? rtrim($hls->getHlsBaseUrl(), '/') . "/" : null;
        $ts = $hls->getTsSubDirectory() ? rtrim($hls->getTsSubDirectory(), '/') . "/" : null;

        return [$ts, $base . $ts];
    }

    /**
     * @param HLS $hls
     * @return array
     */
    private function getFormats(HLS $hls): array
    {
        $format = ['-c:v', $hls->getFormat()->getVideoCodec()];
        $audio_format = $hls->getFormat()->getAudioCodec();

        return $audio_format ? array_merge($format, ['-c:a', $audio_format]) : $format;
    }

    /**
     * @param $base_url
     * @return array
     */
    private function getBaseURL(string $base_url): array
    {
        return $base_url ? ["-hls_base_url", $base_url] : [];
    }

    /**
     * @param HLS $hls
     * @return array
     */
    private function getKeyInfo(HLS $hls): array
    {
        return $hls->getHlsKeyInfoFile() ? ["-hls_key_info_file", $hls->getHlsKeyInfoFile()] : [];
    }

    /**
     * @param Representation $rep
     * @return array
     */
    private function getAudioBitrate(Representation $rep): array
    {
        return $rep->getAudioKiloBitrate() ? ["-b:a", $rep->getAudioKiloBitrate() . "k"] : [];
    }
}