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

class Representation
{
    private $kiloBitrate = 1000;
    private $resize = [];

    /**
     * @return mixed
     */
    public function getResize()
    {
        if (!isset($this->resize['width']) || !isset($this->resize['height'])) {
            return null;
        }

        return $this->resize['width'] . "x" . $this->resize['height'];
    }

    /**
     * @param $width
     * @param $height
     * @return Representation
     * @throws Exception
     */
    public function setResize(int $width, int $height): Representation
    {
        if ($width < 1 || $height < 1) {
            throw new Exception('Wrong resize value');
        }

        $this->resize['width'] = $width;
        $this->resize['height'] = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getKiloBitrate()
    {
        return $this->kiloBitrate;
    }

    /**
     * Sets the kiloBitrate value.
     *
     * @param  integer $kiloBitrate
     * @return Representation
     * @throws Exception
     */
    public function setKiloBitrate($kiloBitrate)
    {
        if ($kiloBitrate < 1) {
            throw new Exception('Wrong kilo bit rate value');
        }

        $this->kiloBitrate = (int)$kiloBitrate;

        return $this;
    }
}