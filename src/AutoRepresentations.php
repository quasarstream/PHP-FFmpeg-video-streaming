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

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Exception\ExceptionInterface;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

class AutoRepresentations
{
    /** @var \FFMpeg\FFProbe\DataMapping\Stream $video */
    private $video;

    /** @var \FFMpeg\FFProbe\DataMapping\Format $format */
    private $format;

    /**
     * regular video's heights
     *
     * @var array side_values
     */
    private $side_values = [144, 240, 360, 480, 720, 1080, 1440, 2160];

    /** @var array $k_bitrate_values */
    private $k_bitrate;

    /**
     * AutoRepresentations constructor.
     * @param Media $media
     * @param array|null $sides
     * @param array|null $k_bitrate
     */
    public function __construct(Media $media, array $sides = null, array $k_bitrate = null)
    {
        $this->video = $media->getStreams()->videos()->first();
        $this->format = $media->getFormat();
        $this->getSideValues($sides);
        $this->getKiloBitrateValues($k_bitrate);
    }

    /**
     * @return Dimension
     */
    private function getDimensions(): Dimension
    {
        try {
            return $this->video->getDimensions();
        } catch (ExceptionInterface $e) {
            throw new RuntimeException("Unable to extract dimensions.: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getKiloBitRate(): int
    {
        if (!$this->video->has('bit_rate')) {
            if (!$this->format->has('bit_rate')) {
                throw new InvalidArgumentException("Unable to extract bitrate.");
            }

            return intval(($this->format->get('bit_rate') / 1024) * .9);
        }

        return (int)$this->video->get('bit_rate') / 1024;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $reps = [];
        $dimension = $this->getDimensions();
        $ratio = $dimension->getRatio()->getValue();

        foreach ($this->side_values as $key => $height) {
            array_push($reps, $this->addRep($this->k_bitrate[$key], Utiles::RTE($height * $ratio), $height));
        }

        return array_merge($reps, [$this->addRep($this->getKiloBitRate(), $dimension->getWidth(), $dimension->getHeight())]);
    }

    /**
     * @param $width
     * @param $k_bitrate
     * @param $height
     * @return Representation
     * @throws InvalidArgumentException
     */
    private function addRep($k_bitrate, $width, $height): Representation
    {
        return (new Representation)->setKiloBitrate($k_bitrate)->setResize($width, $height);
    }

    /**
     * @param array|null $k_bitrate_values
     */
    private function getKiloBitrateValues(?array $k_bitrate_values): void
    {
        $k_bit_rates = [];

        $count_sides = count($this->side_values);

        if (!empty($k_bitrate_values)) {
            if ($count_sides !== count($k_bitrate_values)) {
                throw new InvalidArgumentException("The count of side value array must be the same as the count of kilo bitrate array");
            }

            $this->k_bitrate = $k_bitrate_values;
            return;
        }

        $k_bitrate_value = $this->getKiloBitRate();
        $divided_by = 1.5;

        while ($count_sides) {
            $k_bit_rates[] = (($k_bitrate = intval($k_bitrate_value / $divided_by)) < 64) ? 64 : $k_bitrate;
            $divided_by += .5;
            $count_sides--;
        }

        $this->k_bitrate = array_reverse($k_bit_rates);
    }

    /**
     * @param int $height
     * @return bool
     */
    private function sideFilter(int $height): bool
    {
        return $height < $this->getDimensions()->getHeight();
    }

    /**
     * @param array|null $side_values
     */
    private function getSideValues(?array $side_values): void
    {
        if (!is_null($side_values)) {
            $this->side_values = $side_values;
            return;
        }

        $this->side_values = array_values(array_filter($this->side_values, [$this, 'sideFilter']));
    }
}