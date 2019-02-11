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

namespace AYazdanpanah\FFMpegStreaming\Traits;

use AYazdanpanah\FFMpegStreaming\Format\HEVC;
use AYazdanpanah\FFMpegStreaming\Format\X264;
use FFMpeg\Format\FormatInterface;

trait Formats
{
    /** @var object */
    protected $format;

    /**
     * @param string $audioCodec
     * @param string $videoCodec
     * @return $this
     */
    public function X264($audioCodec = 'libmp3lame', $videoCodec = 'libx264')
    {
        $this->setFormat(new X264($audioCodec, $videoCodec));
        return $this;
    }

    /**
     * @param string $audioCodec
     * @param string $videoCodec
     * @return $this
     */
    public function HEVC($audioCodec = 'libmp3lame', $videoCodec = 'libx265')
    {
        $this->setFormat(new HEVC($audioCodec, $videoCodec));
        return $this;
    }

    /**
     * @return FormatInterface|mixed
     */
    private function getFormat(): FormatInterface
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return $this
     */
    protected function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
}