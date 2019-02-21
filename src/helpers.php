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

use AYazdanpanah\FFMpegStreaming\FFMpeg;
use FFMpeg\Exception\ExceptionInterface;
use AYazdanpanah\FFMpegStreaming\Format\HEVC;
use AYazdanpanah\FFMpegStreaming\Format\X264;

if (! function_exists('dash')) {
    /**
     * Auto generate dash MPD file
     *
     * @param string $input_path
     * @param callable $listener
     * @param string|null $save_path
     * @return string
     */
    function dash(string $input_path, string $save_path = null, callable $listener = null)
    {
        $format = new HEVC();

        if (is_callable($listener)) {
            $format->on('progress', $listener);
        }

        try {
            return FFMpeg::create()
                ->open($input_path)
                ->DASH()
                ->setFormat($format)
                ->autoGenerateRepresentations()
                ->setAdaption('id=0,streams=v id=1,streams=a')
                ->save($save_path);
        } catch (ExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}

if (! function_exists('hls')) {
    /**
     * Auto generate HLS M3U8 file
     *
     * @param string $input_path
     * @param callable|null $listener
     * @param string|null $save_path
     * @return string
     */
    function hls(string $input_path,  string $save_path = null, callable $listener = null)
    {
        $format = new X264();

        if (is_callable($listener)) {
            $format->on('progress', $listener);
        }

        try {
            return FFMpeg::create()
                ->open($input_path)
                ->HLS()
                ->setFormat($format)
                ->autoGenerateRepresentations()
                ->save($save_path);
        } catch (ExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}

if (! function_exists('round_to_even')) {
    /**
     * Round a number to nearest even number
     *
     * @param float $number
     * @return int
     */
    function round_to_even(float $number): int
    {
        return (($number = intval($number)) % 2 == 0) ? $number : $number + 1;
    }
}