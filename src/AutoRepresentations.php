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

use Streaming\Exception\Exception;
use Streaming\MediaInfo\Streams\Stream;
use Streaming\MediaInfo\Streams\StreamCollection;

class AutoRepresentations
{
    /** @var Stream $video */
    private $video;

    /** @var Stream $general */
    private $general;

    /** @var array side_values
     * regular video's heights
     */
    private $side_values = [2160, 1080, 720, 480, 360, 240, 144];

    /**
     * AutoRepresentations constructor.
     * @param StreamCollection $streamCollection
     * @param null | array $side_values
     */
    public function __construct(StreamCollection $streamCollection, $side_values)
    {
        if (null !== $side_values) {
            $this->side_values = $side_values;
        }

        $this->video = $streamCollection->videos()->first();
        $this->general = $streamCollection->general();
    }

    /**
     * @return array
     */
    private function getDimensions(): array
    {
        $width = $this->video->get('Width');
        $height = $this->video->get('Height');

        return [$width, $height, $width / $height];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getKiloBitRate(): int
    {
        if (!$this->video->has('BitRate')) {
            if (!$this->general->has('OverallBitRate')) {
                throw new Exception("Invalid stream");
            }

            return (int)($this->general->get('OverallBitRate') / 1024) * .9;
        }
        return (int)$this->video->get('BitRate') / 1024;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function get(): array
    {
        $kilobitrate = $this->getKiloBitRate();
        list($width, $height, $ratio) = $this->getDimensions();

        $representations[] = $this->addRepresentation($kilobitrate, $width, $height);

        $heights = array_filter($this->side_values, function ($value) use ($height) {
            return $value < $height;
        });

        if (!empty($heights)) {
            $kilobitrates = $this->getKiloBitRates($kilobitrate, count($heights));

            foreach (array_values($heights) as $key => $height) {
                $representations[] = $this->addRepresentation($kilobitrates[$key], Helper::roundToEven($ratio * $height), $height);
            }
        }

        return array_reverse($representations);
    }

    /**
     * @param $width
     * @param $kilobitrate
     * @param $height
     * @return Representation
     * @throws Exception
     */
    private function addRepresentation($kilobitrate, $width, $height): Representation
    {
        return (new Representation())->setKiloBitrate($kilobitrate)->setResize($width, $height);
    }

    /**
     * @param $kilobitrate
     * @param $count
     * @return array
     */
    private function getKiloBitRates($kilobitrate, $count): array
    {
        $divided_by = 1.3;

        while ($count) {
            $kilobitrates[] = (($kbitrate = intval($kilobitrate / $divided_by)) < 64) ? 64 : $kbitrate;
            $divided_by += .3;
            $count--;
        }

        return $kilobitrates;
    }
}