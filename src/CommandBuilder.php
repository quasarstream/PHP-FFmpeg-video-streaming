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


use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\VideoInterface;

class CommandBuilder
{
    /** @var Media */
    private $media;

    /** @var \FFMpeg\Filters\FiltersCollection */
    private $filters;

    /** @var \FFMpeg\Driver\FFMpegDriver */
    private $driver;

    /**
     * CommandBuilder constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->filters = clone $this->media->getFiltersCollection();
        $this->driver = clone $this->media->getFFMpegDriver();
    }

    /**
     * @param VideoInterface $format
     * @param string $path
     * @return array
     * @TODO: optimize this function
     */
    public function build(VideoInterface $format, string $path): array
    {
        $commands = [];
        $this->filters->add(new SimpleFilter($format->getExtraParams(), 10));

        if ($this->driver->getConfiguration()->has('ffmpeg.threads')) {
            $this->filters->add(new SimpleFilter(['-threads', $this->driver->getConfiguration()->get('ffmpeg.threads')]));
        }

        foreach ($this->filters as $filter) {
            $commands = array_merge($this->getInputOptions(), $filter->apply($this->media->baseMedia(), $format));
        }
        array_push($commands, $path);

        return $commands;
    }

    /**
     * @return array
     */
    private function getInputOptions(): array
    {
        $path = $this->media->getPathfile();
        $input_options = Utiles::arrayToFFmpegOpt($this->media->getInputOptions());

        return array_merge($input_options, ['-y', '-i', $path]);
    }
}