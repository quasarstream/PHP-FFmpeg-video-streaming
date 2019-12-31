<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming;

use Streaming\Filters\HLSFilter;
use Streaming\Filters\Filter;

class HLS extends Streaming
{
    /** @var string */
    private $hls_time = 10;

    /** @var bool */
    private $hls_allow_cache = true;

    /** @var string */
    private $hls_key_info_file = "";

    /** @var string */
    private $ts_sub_directory = "";

    /** @var string */
    private $hls_base_url = "";

    /** @var bool */
    public $tmp_key_info_file = false;

    /**
     * @return string
     */
    public function getTsSubDirectory(): string
    {
        return $this->ts_sub_directory;
    }

    /**
     * @param string $ts_sub_directory
     * @return HLS
     */
    public function setTsSubDirectory(string $ts_sub_directory)
    {
        $this->ts_sub_directory = $ts_sub_directory;
        return $this;
    }

    /**
     * @param string $hls_time
     * @return HLS
     */
    public function setHlsTime(string $hls_time): HLS
    {
        $this->hls_time = $hls_time;
        return $this;
    }

    /**
     * @return string
     */
    public function getHlsTime(): string
    {
        return $this->hls_time;
    }

    /**
     * @param bool $hls_allow_cache
     * @return HLS
     */
    public function setHlsAllowCache(bool $hls_allow_cache): HLS
    {
        $this->hls_allow_cache = $hls_allow_cache;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHlsAllowCache(): bool
    {
        return $this->hls_allow_cache;
    }

    /**
     * @param string $hls_key_info_file
     * @return HLS
     */
    public function setHlsKeyInfoFile(string $hls_key_info_file): HLS
    {
        $this->hls_key_info_file = $hls_key_info_file;
        return $this;
    }

    /**
     * @param string $save_to
     * @param string $url
     * @param int $length
     * @return HLS
     */
    public function encryption(string $save_to, string $url, int $length = 16): HLS
    {
        $this->setHlsKeyInfoFile(HLSKeyInfo::generate($save_to, $url, $length));
        $this->tmp_key_info_file = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getHlsKeyInfoFile(): string
    {
        return $this->hls_key_info_file;
    }

    /**
     * @param string $hls_base_url
     * @return HLS
     */
    public function setHlsBaseUrl(string $hls_base_url): HLS
    {
        $this->hls_base_url = $hls_base_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getHlsBaseUrl(): string
    {
        return $this->hls_base_url;
    }

    /**
     * @return Filter
     */
    protected function getFilter(): Filter
    {
        return new HLSFilter($this);
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        $path = $this->getFilePath();
        $reps = $this->getRepresentations();

        HLSPlaylist::save($path . ".m3u8", $reps);

        return $path . "_" . end($reps)->getHeight() . "p.m3u8";
    }
}