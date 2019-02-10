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
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFProbe\DataMapping\Stream;

class AutoRepresentations
{
    private $stream;

    private $heights = [2160, 1080, 720, 480, 240, 144];

    private $height;

    /**
     * AutoRepresentations constructor.
     * @param Stream $stream
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return Dimension
     */
    private function getDimensions(): Dimension
    {
        return $this->stream->getDimensions();
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

        $dimension = $this->getDimensions();
        $this->height = $dimension->getHeight();
        $ratio = $dimension->getRatio()->getValue();
        $kilobitrate = $this->getKiloBitRate();

        $representations[] = $this->addRepresentation($ratio, $kilobitrate, $this->height);

        $heights = array_filter($this->heights, function ($value) {
            return $value < $this->height;
        });

        if (count($heights) > 0) {
            $kilobitrates = $this->getKiloBitRates($kilobitrate, count($heights));

            foreach (array_values($heights) as $key => $height) {
                $representations[] = $this->addRepresentation($ratio, $kilobitrates[$key], $height);
            }
        }

        return array_reverse($representations);
    }

    /**
     * @param $ratio
     * @param $kilobitrate
     * @param $height
     * @return Representation
     * @throws Exception
     */
    private function addRepresentation($ratio, $kilobitrate, $height): Representation
    {
        $width = (int)$height * $ratio;

        if ($width % 2 == 1) $width++;

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
        $kilobitrates = [];

        for ($i = 0; $i < $count; $i++) {
            $kbitrate = intval($kilobitrate / $divided_by);

            if ($kbitrate < 100) $kbitrate = 100;

            $kilobitrates[] = $kbitrate;
            $divided_by += .3;
        }

        return $kilobitrates;
    }
}