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
use AYazdanpanah\FFMpegStreaming\Traits\Formats;
use FFMpeg\Format\FormatInterface;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var Filter */
    protected $filter;

    /** @var array */
    protected $representations = [];

    /**
     * @param Representation $representation
     * @return $this
     * @throws Exception
     */
    public function addRepresentation(Representation $representation): Export
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

    /**
     * Export constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
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
     * @return Export
     */
    protected function setFormat($format): Export
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $path
     * @return Export
     */
    public function save(string $path = null): Export
    {
        $this->setFilter();

        $this->media->addFilter(
            $this->getFilter()
        );

        $this->media->save(
            $this->getFormat(),
            $this->getPath($path)
        );

        return $this;
    }

    /**
     * @return Filter
     */
    abstract protected function getFilter(): Filter;

    /**
     * @return mixed
     */
    abstract protected function setFilter();

    private function getPath($path): string
    {
        if (null === $path) {
            if ($this instanceof DASH) {
                $path = $this->media->getPath() . '.mpd';
            } elseif ($this instanceof HLS) {
                $path = $this->media->getPath() . '.m3u8';
            }
        }

        return $path;
    }
}
