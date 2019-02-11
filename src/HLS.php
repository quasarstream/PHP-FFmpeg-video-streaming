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

use AYazdanpanah\FFMpegStreaming\Traits\Representation;

class HLS extends Export
{
    use Representation;

    /** @var string */
    protected $stream_map;

    /**
     * @return mixed
     */
    public function getStreamMap()
    {
        return $this->stream_map;
    }

    /**
     * @param mixed $adaption
     * @return HLS
     */
    public function setStreamMap(string $adaption): HLS
    {
        $this->stream_map = $adaption;
        return $this;
    }

    /**
     * @return Filter
     */
    protected function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return mixed|void
     */
    protected function setFilter()
    {
        $this->filter = new Filter($this);
    }
}
