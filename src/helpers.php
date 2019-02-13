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
use \AYazdanpanah\FFMpegStreaming\Exception\FFMpegExceptionInterface;

if (! function_exists('dash')) {
    /**
     * Auto generate dash MPD file
     *
     * @param string $input_path
     * @param string|null $save_path
     * @return string
     */
    function dash(string $input_path, string $save_path = null): string
    {
        try {
            FFMpeg::create()
                ->open($input_path)
                ->DASH()
                ->X264()
                ->autoGenerateRepresentations()
                ->setAdaption('id=0,streams=v id=1,streams=a')
                ->save($save_path);
            return "Done!";
        } catch (FFMpegExceptionInterface $e) {
            return "Failed: error: " . $e->getMessage();
        }
    }
}

if (! function_exists('hls')) {
    /**
     * Auto generate HLS M3U8 file
     *
     * @param string $input_path
     * @param string|null $save_path
     * @return string
     */
    function hls(string $input_path, string $save_path = null): string
    {
        try {
            FFMpeg::create()
                ->open($input_path)
                ->HLS()
                ->X264()
                ->autoGenerateRepresentations()
                ->save($save_path);
            return "Done!";
        } catch (FFMpegExceptionInterface $e) {
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