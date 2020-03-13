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


use Streaming\Filters\StreamFilterInterface;
use Streaming\Filters\StreamToFileFilter;

class StreamToFile extends Stream
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * @param array $params
     * @return StreamToFile
     */
    public function setParams(array $params): StreamToFile
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return implode(".", [$this->getFilePath(), $this->pathInfo(PATHINFO_EXTENSION) ?? "mp4"]);
    }

    /**
     * @return StreamToFileFilter
     */
    protected function getFilter(): StreamFilterInterface
    {
        return new StreamToFileFilter($this);
    }
}