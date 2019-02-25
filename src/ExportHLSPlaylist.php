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


class ExportHLSPlaylist
{
    /**
     * @param $filename
     * @param $representations
     * @param $basename
     */
    public static function savePlayList($filename, $representations, $basename)
    {
        file_put_contents($filename, static::generateContents($representations, $basename));
    }

    /**
     * @param $representations
     * @param $basename
     * @return string
     */
    private static function generateContents($representations, $basename)
    {
        $content[] = "#EXTM3U";
        $content[] = "#EXT-X-VERSION:3";

        foreach ($representations as $representation) {
            if ($representation instanceof Representation) {
                $content[] = "#EXT-X-STREAM-INF:BANDWIDTH=" . $representation->getKiloBitrate() * 1024 . ",RESOLUTION=" . $representation->getResize();
                $content[] = $basename . "_" . $representation->getHeight() . "p.m3u8";
            }
        }

        return implode(PHP_EOL, $content);
    }
}