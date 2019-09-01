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
use Streaming\Traits\Representations;
use Streaming\Filters\Filter;

class DASH extends Export
{
    use Representations;

    /** @var string */
    protected $adaption;

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
     * @return Filter
     */
    protected function getFilter(): Filter
    {
        return new DASHFilter($this);
    }
}
