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

class StreamToFileFilter extends FormatFilter
{

    /**
     * @param $media
     * @return mixed
     */
    public function streamFilter(StreamInterface $media): void
    {
        $this->filter = array_merge(
            $this->getFormatOptions($media->getFormat()),
            $media->getParams()
        );
    }
}