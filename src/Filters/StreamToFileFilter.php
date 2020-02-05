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


use Streaming\StreamToFile;

class StreamToFileFilter extends Filter
{

    /**
     * @param $media
     * @return mixed
     */
    public function setFilter($media): void
    {
        $this->filter = $this->StreamToFileFilter($media);
    }

    private function StreamToFileFilter(StreamToFile $media)
    {
        return ['-c', 'copy'];
    }
}