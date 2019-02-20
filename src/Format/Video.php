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

namespace AYazdanpanah\FFMpegStreaming\Format;

use FFMpeg\Format\Audio\DefaultAudio;

abstract class Video extends DefaultAudio
{
    /** @var string */
    protected $audioCodec;

    /** @var string */
    protected $videoCodec;

    /** @var integer */
    protected $passes = 1;

    /** @var integer */
    protected $audioKiloBitrate = 128;

    /** @var integer */
    protected $audioChannels = 2;

    /**
     * @return string
     */
    public function getAudioCodec(): string
    {
        return $this->audioCodec;
    }

    /**
     * @param string $audioCodec
     * @return Video
     */
    public function setAudioCodec($audioCodec)
    {
        $this->audioCodec = $audioCodec;
        return $this;
    }

    /**
     * @return string
     */
    public function getVideoCodec(): string
    {
        return $this->videoCodec;
    }

    /**
     * @param string $videoCodec
     */
    public function setVideoCodec(string $videoCodec): void
    {
        $this->videoCodec = $videoCodec;
    }

    /**
     * Returns an array of extra parameters to add to ffmpeg commandline.
     *
     * @return array()
     */
    public function getExtraParams()
    {
        return [
            '-c:v',
            $this->getVideoCodec()
        ];
    }

    /**
     * Returns the number of passes.
     *
     * @return string
     */
    public function getPasses()
    {
        return $this->passes;
    }

    /**
     * Gets the audio kiloBitrate value.
     *
     * @return integer
     */
    public function getAudioKiloBitrate()
    {
        return $this->audioKiloBitrate;
    }

    /**
     * Gets the audio channels value.
     *
     * @return integer
     */
    public function getAudioChannels()
    {
        return $this->audioChannels;
    }
}