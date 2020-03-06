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

use FFMpeg\Filters\FilterInterface;
use Streaming\StreamInterface;

interface StreamFilterInterface extends FilterInterface
{
    /**
     * @param StreamInterface $stream
     * @return mixed
     */
    public function streamFilter(StreamInterface $stream): void;

    /**
     * @return array
     */
    public function apply(): array;
}