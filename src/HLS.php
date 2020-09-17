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
use Streaming\Filters\StreamFilterInterface;

class HLS extends Streaming
{

    /** @var string */
    private $hls_time = 10;

    /** @var bool */
    private $hls_allow_cache = true;

    /** @var string */
    private $hls_key_info_file = "";

    /** @var string */
    private $seg_sub_directory = "";

    /** @var string */
    private $hls_base_url = "";

    /** @var int */
    private $hls_list_size = 0;

    /** @var bool */
    private $tmp_key_info_file = false;

    /** @var string */
    public $master_playlist;

    /** @var string */
    private $hls_segment_type = 'mpegts';

    /** @var string */
    private $hls_fmp4_init_filename = "init.mp4";

    /** @var array */
    private $stream_des = [];

    /** @var array */
    private $flags = [];

    /** @var array */
    private $subtitles = [];

    /**
     * @return string
     */
    public function getSegSubDirectory(): string
    {
        return $this->seg_sub_directory;
    }

    /**
     * @param string $seg_sub_directory
     * @return HLS
     */
    public function setSegSubDirectory(string $seg_sub_directory)
    {
        $this->seg_sub_directory = $seg_sub_directory;
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
     * @param int $key_rotation_period
     * @param string $search
     * @param int $length
     * @return HLS
     */
    public function encryption(string $save_to, string $url, int $key_rotation_period = 0, string $search = ".ts' for writing", int $length = 16): HLS
    {
        $key_info = HLSKeyInfo::create($save_to, $url);
        $key_info->setLength($length);

        if ($key_rotation_period > 0) {
            $key_info->rotateKey($this->getMedia()->getFFMpegDriver(), $key_rotation_period, $search);
            array_push($this->flags, HLSFlag::PERIODIC_REKEY);
        }

        $this->setHlsKeyInfoFile((string)$key_info);
        $this->tmp_key_info_file = true;

        return $this;
    }

    public function subtitle(HLSSubtitle $subtitle)
    {
        array_push($this->subtitles, $subtitle);
        return $this;
    }

    /**
     * @param array $subtitles
     * @return HLS
     */
    public function subtitles(array $subtitles): HLS
    {
        array_walk($subtitles, [$this, 'subtitle']);
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
     * @param int $hls_list_size
     * @return HLS
     */
    public function setHlsListSize(int $hls_list_size): HLS
    {
        $this->hls_list_size = $hls_list_size;
        return $this;
    }

    /**
     * @return int
     */
    public function getHlsListSize(): int
    {
        return $this->hls_list_size;
    }

    /**
     * @param string $master_playlist
     * @param array $stream_des
     * @return HLS
     */
    public function setMasterPlaylist(string $master_playlist, array $stream_des = []): HLS
    {
        $this->master_playlist = $master_playlist;
        $this->stream_des = $stream_des;

        return $this;
    }

    /**
     * @return HLS
     */
    public function fragmentedMP4(): HLS
    {
        $this->setHlsSegmentType("fmp4");
        return $this;
    }

    /**
     * @param string $hls_segment_type
     * @return HLS
     */
    public function setHlsSegmentType(string $hls_segment_type): HLS
    {
        $this->hls_segment_type = $hls_segment_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getHlsSegmentType(): string
    {
        return $this->hls_segment_type;
    }

    /**
     * @param string $hls_fmp4_init_filename
     * @return HLS
     */
    public function setHlsFmp4InitFilename(string $hls_fmp4_init_filename): HLS
    {
        $this->hls_fmp4_init_filename = $hls_fmp4_init_filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getHlsFmp4InitFilename(): string
    {
        return $this->hls_fmp4_init_filename;
    }

    /**
     * @param array $flags
     * @return HLS
     */
    public function setFlags(array $flags): HLS
    {
        $this->flags = array_merge($this->flags, $flags);
        return $this;
    }

    /**
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @return HLSFilter
     */
    protected function getFilter(): StreamFilterInterface
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

        if(!empty($this->subtitles)){
            $this->generateSubs($path);
        }

        $this->savePlaylist($path . ".m3u8");

        return $path . "_" . $reps->end()->getHeight() . "p.m3u8";
    }

    /**
     * @param $path
     */
    public function savePlaylist(string $path): void
    {
        $mater_playlist = new HLSPlaylist($this);
        $mater_playlist->save($this->master_playlist ?? $path, $this->stream_des);
    }

    /**
     * @param string $path
     */
    private function generateSubs(string $path)
    {
        $this->stream_des = array_merge($this->stream_des, [PHP_EOL]);

        foreach ($this->subtitles as $subtitle) {
            if($subtitle instanceof HLSSubtitle){
                $subtitle->generateM3U8File("{$path}_subtitles_{$subtitle->getLanguageCode()}.m3u8", $this->getDuration());
                array_push($this->stream_des, (string)$subtitle);
            }
        }
        array_push($this->stream_des, PHP_EOL);

        $this->getRepresentations()->map(function (Representation $rep){
            return $rep->setHlsStreamInfo(["SUBTITLES" => "\"" . $this->subtitles[0]->getGroupId() . "\""]);
        });
    }

    /**
     * @return float
     */
    private function getDuration():float
    {
        return $this->getMedia()->getFormat()->get("duration", 0);
    }

    /**
     * Clear key info file if is a temp file
     */
    public function __destruct()
    {
        if ($this->tmp_key_info_file) {
            File::remove($this->getHlsKeyInfoFile());
        }

        parent::__destruct();
    }
}