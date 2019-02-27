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

use AYazdanpanah\FFMpegStreaming\Filters\Filter;
use AYazdanpanah\FFMpegStreaming\Traits\Formats;

abstract class Export
{
    use Formats;

    /** @var object */
    protected $media;

    /** @var Filter */
    protected $filter;

    /** @var array */
    protected $path_info;

    /**
     * Export constructor.
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->path_info = $media->getPathInfo();
    }

    /**
     * @param string $path
     * @return Export
     */
    public function save(string $path = null): Export
    {
        $path = $this->getPath($path);

        $this->setFilter();

        $this->media->addFilter(
            $this->getFilter()
        );

        $this->media->save(
            $this->getFormat(),
            $path
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
        if(null !== $path){
            $this->path_info = pathinfo($path);
        }

        $dirname = str_replace("\\", "/", $this->path_info["dirname"]);
        $filename = substr($this->path_info["filename"], -50);

        Helper::makeDir($dirname);

        if ($this instanceof DASH) {
            $path = $dirname . "/" . $filename . ".mpd";
        } elseif ($this instanceof HLS) {
            $representations = $this->getRepresentations();
            $path = $dirname . "/" . $filename . "_" . end($representations)->getHeight() . "p.m3u8";
            ExportHLSPlaylist::savePlayList($dirname . "/" . $filename . ".m3u8", $this->getRepresentations(), $filename);
        }

        return $path;
    }

    /**
     * @return array
     */
    public function getPathInfo(): array
    {
        return $this->path_info;
    }
}
