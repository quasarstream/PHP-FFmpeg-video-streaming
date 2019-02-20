<?php

/**
 * Copyright 2019 Amin Yazdanpanah<http://www.aminyazdanpanah.com>.
 *
 * Licensed under the MIT License;
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AYazdanpanah\FFMpegStreaming;

use AYazdanpanah\FFMpegStreaming\Exception\Exception;
use FFMpeg\FFProbe\DataMapping\Stream;

class AutoRepresentations
{
    /** @var Stream $stream */
    private $stream;

    /** @Const regular video's heights */
    private const heights = [2160, 1080, 720, 480, 240, 144];

    /**
     * AutoRepresentations constructor.
     * @param Stream $stream
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return array
     */
    private function getDimensions(): array
    {
        $width = $this->stream->get('width');
        $height = $this->stream->get('height');

        return [$width, $height, $width / $height];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getKiloBitRate(): int
    {
        if (!$this->stream->has('bit_rate')) {
            throw new Exception("Invalid stream");
        }
        return (int)$this->stream->get('bit_rate') / 1000;
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

        $heights = array_filter(static::heights, function ($value) use ($height) {
            return $value < $height;
        });

        if ($heights) {
            $kilobitrates = $this->getKiloBitRates($kilobitrate, count($heights));

            foreach (array_values($heights) as $key => $height) {
                $representations[] = $this->addRepresentation($kilobitrates[$key], round_to_even($ratio * $height), $height);
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
    private function getKiloBitRates($kilobitrate, $count)
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