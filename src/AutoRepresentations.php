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
    private $side_values = [2160, 1440, 1080, 720, 480, 360, 240, 144];

    /** @var array $k_bitrate_values */
    private $k_bitrate_values;

    /**
     * AutoRepresentations constructor.
     * @param array $probe
     * @param null | array $side_values
     * @param array $k_bitrate_values
     */
    public function __construct(array $probe, array $side_values = null, array $k_bitrate_values = null)
    {
        $this->video = $probe['streams']->videos()->first();
        $this->format = $probe['format'];
        $this->getSideValues($side_values);
        $this->getKiloBitrateValues($k_bitrate_values);
    }

    /**
     * @return array
     */
    private function getDimensions(): array
    {
        $width = $this->video->get('width');
        $height = $this->video->get('height');

        return [$width, $height, $width / $height];
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getKiloBitRate(): int
    {
        if (!$this->video->has('bit_rate')) {
            if (!$this->format->has('bit_rate')) {
                throw new InvalidArgumentException("We could not determine the value of video's bitrate");
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
        $k_bitrate = $this->getKiloBitRate();
        list($w, $h, $r) = $this->getDimensions();

        $reps = [$this->addRep($k_bitrate, $w, $h)];

        foreach ($this->side_values as $key => $height) {
            $reps[] = $this->addRep($this->k_bitrate_values[$key], Utilities::roundToEven($r * $height), $height);
        }

        return array_reverse($reps);
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
        $count_sides = count($this->side_values);

        if ($k_bitrate_values) {
            if ($count_sides !== count($k_bitrate_values)) {
                throw new InvalidArgumentException("The count of side value array must be the same as the count of kilo bitrate array");
            }

            $this->k_bitrate_values = $k_bitrate_values;
            return;
        }

        $k_bitrate_value = $this->getKiloBitRate();
        $divided_by = 1.5;

        while ($count_sides) {
            $this->k_bitrate_values[] = (($k_bitrate = intval($k_bitrate_value / $divided_by)) < 64) ? 64 : $k_bitrate;
            $divided_by += .5;
            $count_sides--;
        }
    }

    /**
     * @param array|null $side_values
     */
    private function getSideValues(?array $side_values): void
    {
        if ($side_values) {
            $this->side_values = $side_values;
            return;
        }

        $h = $this->getDimensions()[1];

        $this->side_values = array_values(array_filter($this->side_values, function ($height) use ($h) {
            return $height < $h;
        }));
    }
}