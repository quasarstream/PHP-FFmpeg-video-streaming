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


use AYazdanpanah\FFMpegStreaming\AutoRepresentations;
use AYazdanpanah\FFMpegStreaming\Exception\Exception;

trait Representation
{
    /** @var array */
    protected $representations = [];

    /**
     * @param Representation $representation
     * @return $this
     * @throws Exception
     */
    public function addRepresentation(Representation $representation)
    {
        if (!$this->format) {
            throw new Exception('Format has not been set');
        }

        $this->representations[] = $representation;
        return $this;
    }

    /**
     * @return array
     */
    public function getRepresentations(): array
    {
        return $this->representations;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function autoGenerateRepresentations()
    {
        $this->representations = (new AutoRepresentations($this->media->getFirstStream()))
            ->get();

        return $this;
    }

}