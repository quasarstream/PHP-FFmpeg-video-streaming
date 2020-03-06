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

use Streaming\StreamInterface;
use Streaming\File;
use Streaming\Representation;
use Streaming\Utiles;

class HLSFilter extends StreamFilter
{
    /**  @var \Streaming\HLS */
    private $hls;

    /** @var string */
    private $dirname;

    /** @var string */
    private $filename;

    /** @var string */
    private $seg_sub_dir;

    /** @var string */
    private $base_url;

    /** @var string */
    private $seg_filename;

    /**
     * @param
     * @return array
     */
    private function getFormats(): array
    {
        $format = ['-c:v', $this->hls->getFormat()->getVideoCodec()];
        $audio_format = $this->hls->getFormat()->getAudioCodec();

        return $audio_format ? array_merge($format, ['-c:a', $audio_format]) : $format;
    }

    /**
     * @param Representation $rep
     * @param bool $not_last
     * @return array
     */
    private function playlistPath(Representation $rep, bool $not_last): array
    {
        return $not_last ? [$this->dirname . "/" . $this->filename . "_" . $rep->getHeight() . "p.m3u8"] : [];
    }

    /**
     * @param Representation $rep
     * @return array
     */
    private function getAudioBitrate(Representation $rep): array
    {
        return $rep->getAudioKiloBitrate() ? ["-b:a", $rep->getAudioKiloBitrate() . "k"] : [];
    }

    /**
     * @return array
     */
    private function getBaseURL(): array
    {
        return $this->base_url ? ["-hls_base_url", $this->base_url] : [];
    }

    /**
     * @param
     * @return array
     */
    private function getKeyInfo(): array
    {
        return $this->hls->getHlsKeyInfoFile() ? ["-hls_key_info_file", $this->hls->getHlsKeyInfoFile()] : [];
    }

    /**
     * @return string
     */
    private function getInitFilename(): string
    {
        return $this->seg_sub_dir . $this->filename . "_" . $this->hls->getHlsFmp4InitFilename();
    }

    /**
     * @param Representation $rep
     * @return string
     */
    private function getSegmentFilename(Representation $rep): string
    {
        $ext = ($this->hls->getHlsSegmentType() === "fmp4") ? "m4s" : "ts";
        return $this->seg_filename . "_" . $rep->getHeight() . "p_%04d." . $ext;
    }

    /**
     * @param Representation $rep
     * @return array
     */
    private function initArgs(Representation $rep): array
    {
        return [
            "-s:v", $rep->getResize(),
            "-crf", "20",
            "-sc_threshold", "0",
            "-g", "48",
            "-keyint_min", "48",
            "-hls_list_size", $this->hls->getHlsListSize(),
            "-hls_time", $this->hls->getHlsTime(),
            "-hls_allow_cache", (int)$this->hls->isHlsAllowCache(),
            "-b:v", $rep->getKiloBitrate() . "k",
            "-maxrate", intval($rep->getKiloBitrate() * 1.2) . "k",
            "-hls_segment_type", $this->hls->getHlsSegmentType(),
            "-hls_fmp4_init_filename", $this->getInitFilename(),
            "-hls_segment_filename", $this->getSegmentFilename($rep)
        ];
    }

    /**
     * @param Representation $rep
     * @param bool $not_last
     */
    private function getArgs(Representation $rep, bool $not_last): void
    {
        $this->filter = array_merge(
            $this->filter,
            $this->initArgs($rep),
            $this->getAudioBitrate($rep),
            $this->getBaseURL(),
            $this->getKeyInfo(),
            $this->hls->getAdditionalParams(),
            ["-strict", $this->hls->getStrict()],
            $this->playlistPath($rep, $not_last)
        );
    }

    /**
     * set segments paths
     */
    private function segmentPaths()
    {
        if ($this->hls->getTsSubDirectory()) {
            File::makeDir($this->dirname . "/" . $this->hls->getTsSubDirectory() . "/");
        }

        $base = Utiles::appendSlash($this->hls->getHlsBaseUrl());

        $this->seg_sub_dir = Utiles::appendSlash($this->hls->getTsSubDirectory());
        $this->seg_filename = $this->dirname . "/" . $this->seg_sub_dir . $this->filename;
        $this->base_url = $base . $this->seg_sub_dir;
    }

    /**
     * set paths
     */
    private function setPaths(): void
    {
        $this->dirname = str_replace("\\", "/", $this->hls->getPathInfo(PATHINFO_DIRNAME));
        $this->filename = $this->hls->getPathInfo(PATHINFO_FILENAME);
        $this->segmentPaths();
    }

    /**
     * @param StreamInterface $stream
     * @return void
     */
    public function streamFilter(StreamInterface $stream): void
    {
        $this->hls = $stream;
        $this->setPaths();

        $reps = $this->hls->getRepresentations();

        foreach ($reps as $key => $rep) {
            if ($key) {
                $this->filter = array_merge($this->filter, $this->getFormats());
            }

            $this->getArgs($rep, end($reps) !== $rep);
        }
    }
}