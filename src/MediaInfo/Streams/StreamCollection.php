<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Streaming\MediaInfo\Streams;


class StreamCollection implements \Countable, \IteratorAggregate
{
    private $streams;

    /**
     * @param array $streams
     */
    public function __construct(array $streams)
    {
        $this->streams = $streams;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->streams;
    }

    /**
     * @return StreamCollection
     */
    public function audios()
    {
        $audios = array_filter($this->streams, function (Stream $stream) {
            return $stream->isAudio();
        });

        return new static(array_values($audios));
    }

    /**
     * @return StreamCollection
     */
    public function videos()
    {
        $videos =  array_filter($this->streams, function (Stream $stream) {
            return $stream->isVideo();
        });

        return new static(array_values($videos));
    }

    /**
     * @return mixed|null
     */
    public function general()
    {
        foreach ($this->streams as $stream){
            if ($stream instanceof Stream && $stream->get('@type') === "General"){
                return $stream;
            }
        }

        return null;
    }

    /**
     * @return null | Stream
     */
    public function first()
    {
        $stream = current($this->streams);

        return $stream ?: null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->streams);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->streams);
    }
}
