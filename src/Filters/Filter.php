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

use Streaming\Export;
use FFMpeg\Filters\FilterInterface;

abstract class Filter implements FilterInterface, FilterStreamingInterface
{
    private $priority = 2;

    protected $filter = [];

    /**
     * Filter constructor.
     * @param Export $media
     */
    public function __construct(Export $media)
    {
        $this->setFilter($media);
    }

    /**
     * Applies the filter on the the Audio media given an format.
     *
     * @return array An array of arguments
     */
    public function apply(): array
    {
        return $this->getFilter();
    }

    /**
     * Returns the priority of the filter.
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}