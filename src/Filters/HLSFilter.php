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

class HLSFilter extends FormatFilter
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
        return $rep->getAudioKiloBitrate() ? ["b:a" => $rep->getAudioKiloBitrate() . "k"] : [];
    }

    /**
     * @return array
     */
    private function getBaseURL(): array
    {
        return $this->base_url ? ["hls_base_url" => $this->base_url] : [];
    }

    private function flags(): array
    {
        return !empty($this->hls->getFlags()) ? ["hls_flags" => implode("+", $this->hls->getFlags())] : [];
    }

    /**
     * @return array
     */
    private function getKeyInfo(): array
    {
        return $this->hls->getHlsKeyInfoFile() ? ["hls_key_info_file" => $this->hls->getHlsKeyInfoFile()] : [];
    }

    /**
     * @param Representation $rep
     * @return string
     */
    private function getInitFilename(Representation $rep): string
    {
        return $this->seg_sub_dir . $this->filename . "_" . $rep->getHeight() ."p_". $this->hls->getHlsFmp4InitFilename();
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
        $init = [
            "hls_list_size"             => $this->hls->getHlsListSize(),
            "hls_time"                  => $this->hls->getHlsTime(),
            "hls_allow_cache"           => (int)$this->hls->isHlsAllowCache(),
            "hls_segment_type"          => $this->hls->getHlsSegmentType(),
            "hls_fmp4_init_filename"    => $this->getInitFilename($rep),
            "hls_segment_filename"      => $this->getSegmentFilename($rep),
            "s:v"                       => $rep->size2string(),
            "b:v"                       => $rep->getKiloBitrate() . "k"
        ];

        return array_merge($init,
            $this->getAudioBitrate($rep),
            $this->getBaseURL(),
            $this->flags(),
            $this->getKeyInfo());
    }

    /**
     * @param Representation $rep
     * @param bool $not_last
     */
    private function getArgs(Representation $rep, bool $not_last): void
    {
        $this->filter = array_merge(
            $this->filter,
            $this->getFormatOptions($this->hls->getFormat()),
            Utiles::arrayToFFmpegOpt($this->initArgs($rep)),
            Utiles::arrayToFFmpegOpt($this->hls->getAdditionalParams()),
            ["-strict", $this->hls->getStrict()],
            $this->playlistPath($rep, $not_last)
        );
    }

    /**
     * set segments paths
     */
    private function segmentPaths()
    {
        if ($this->hls->getSegSubDirectory()) {
            File::makeDir($this->dirname . "/" . $this->hls->getSegSubDirectory() . "/");
        }

        $base = Utiles::appendSlash($this->hls->getHlsBaseUrl());

        $this->seg_sub_dir = Utiles::appendSlash($this->hls->getSegSubDirectory());
        $this->seg_filename = $this->dirname . "/" . $this->seg_sub_dir . $this->filename;
        $this->base_url = $base . $this->seg_sub_dir;
    }

    /**
     * set paths
     */
    private function setPaths(): void
    {
        $this->dirname = str_replace("\\", "/", $this->hls->pathInfo(PATHINFO_DIRNAME));
        $this->filename = $this->hls->pathInfo(PATHINFO_FILENAME);
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
            $this->getArgs($rep, $reps->end() !== $rep);
        }
    }
}