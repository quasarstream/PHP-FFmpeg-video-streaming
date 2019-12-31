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

use Streaming\Traits\Representations;


abstract class Streaming extends Export
{
    use Representations;

    /** @var string */
    private $strict = "-2";

    /** @var array */
    private $additional_params = [];

    /**
     * @return array
     */
    public function getAdditionalParams(): array
    {
        return $this->additional_params;
    }

    /**
     * @param array $additional_params
     * @return Export
     */
    public function setAdditionalParams(array $additional_params)
    {
        $this->additional_params = $additional_params;
        return $this;
    }

    /**
     * @param string $strict
     * @return Export
     */
    public function setStrict(string $strict): Export
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrict(): string
    {
        return $this->strict;
    }

}