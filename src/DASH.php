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
use Streaming\Filters\Filter;

class DASH extends Streaming
{
    /** @var string */
    private $adaption;

    /** @var string */
    private $seg_duration = 10;

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
     * @return Filter
     */
    protected function getFilter(): Filter
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