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

use Streaming\Filters\DASHFilter;
use Streaming\Filters\StreamFilterInterface;

class DASH extends Streaming
{
    /** @var string */
    private $adaption;

    /** @var string */
    private $seg_duration = 10;

    /** @var bool */
    private $generate_hls_playlist = false;

    /**
     * @return mixed
     */
    public function getAdaption()
    {
        return $this->adaption;
    }

    /**
     * @param mixed $adaption
     * @return DASH
     */
    public function setAdaption(string $adaption): DASH
    {
        $this->adaption = $adaption;
        return $this;
    }

    /**
     * @param string $seg_duration
     * @return DASH
     */
    public function setSegDuration(string $seg_duration): DASH
    {
        $this->seg_duration = $seg_duration;
        return $this;
    }

    /**
     * @return string
     */
    public function getSegDuration(): string
    {
        return $this->seg_duration;
    }

    /**
     * @param bool $generate_hls_playlist
     * @return DASH
     */
    public function generateHlsPlaylist(bool $generate_hls_playlist = true): DASH
    {
        $this->generate_hls_playlist = $generate_hls_playlist;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGenerateHlsPlaylist(): bool
    {
        return $this->generate_hls_playlist;
    }

    /**
     * @return DASHFilter
     */
    protected function getFilter(): StreamFilterInterface
    {
        return new DASHFilter($this);
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return implode(".", [$this->getFilePath(), "mpd"]);
    }
}