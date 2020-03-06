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


use Streaming\Exception\InvalidArgumentException;
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
        return implode(".", [$this->getFilePath(), $this->getPathInfo(PATHINFO_EXTENSION) ?? "mp4"]);
    }

    /**
     * @return StreamToFileFilter
     */
    protected function getFilter(): StreamFilterInterface
    {
        if ($this->uri) {
            throw new InvalidArgumentException("It is not possible to live this file");
        }

        return new StreamToFileFilter($this);
    }
}