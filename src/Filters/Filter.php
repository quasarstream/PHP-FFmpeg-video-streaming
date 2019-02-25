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

namespace AYazdanpanah\FFMpegStreaming\Filters;

use AYazdanpanah\FFMpegStreaming\Export;
use FFMpeg\Filters\FilterInterface;

abstract class Filter implements FilterInterface, FilterStreamingInterface
{
    private $priority = 2;

    protected $filter = [];

    /**
     * Filter constructor.
     * @param Export $media
     */
    public function __construct(Export $media)
    {
        $this->setFilter($media);
    }

    /**
     * Applies the filter on the the Audio media given an format.
     *
     * @return array An array of arguments
     */
    public function apply(): array
    {
        return $this->getFilter();
    }

    /**
     * Returns the priority of the filter.
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}