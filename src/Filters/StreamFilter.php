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

abstract class StreamFilter implements StreamFilterInterface
{
    private $priority = 2;

    protected $filter = [];

    /**
     * Filter constructor.
     * @param StreamInterface $stream
     */
    public function __construct(StreamInterface $stream)
    {
        $this->streamFilter($stream);
    }

    /**
     * Applies the filter on the the stream media
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