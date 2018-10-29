<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/24/2018
 * Time: 1:39 AM
 */

namespace App\Library;


class Helper
{
    public static function formatBytes($bytes, $precision = 2)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, $precision) . trans('dictionary.gb');;
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, $precision) . trans('dictionary.mb');
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, $precision) . trans('dictionary.kb');
        } elseif ($bytes > 1) {
            $bytes = $bytes . trans('dictionary.bytes');
        } elseif ($bytes == 1) {
            $bytes = $bytes . trans('dictionary.byte');
        } else {
            $bytes = '0' . trans('dictionary.bytes');
        }

        return $bytes;
    }

    /**
     * @Author Amin Yazdanpanah
     * @param $sec
     * @return string
     */
    public static function formatSec($sec)
    {
        if ($sec >= 3600) {
            $format = 'H:i:s';
        } elseif ($sec >= 60) {
            $format = 'i:s';
        } else {
            return $sec . ' ' . trans('dictionary.seconds');
        }
        return gmdate($format, $sec);
    }

    /**
     * @Author Amin Yazdanpanah
     * @param $getValue
     * @return mixed|string
     */
    private function getNameRatio($getValue)
    {
        if (in_array($getValue, [1, 2])) {
            return "{$getValue}:1";
        }
        $getValue = round($getValue, 2, PHP_ROUND_HALF_DOWN);
        if (config('constants.aspect_ratio_name.' . $getValue)) {
            return config('constants.aspect_ratio_name.' . sprintf('%0.2f', $getValue));
        }
        return "{$getValue}:1";
    }

    /**
     * @Author Amin Yazdanpanah
     * @param $video_quality
     * @param bool $final_format
     * @return array
     */
    public function getVideoQualities($video_quality, $final_format = false)
    {
        $dimension = explode('x', $video_quality['dimension']);
        $width = current($dimension);
        $height = end($dimension);
        $param = [
            'aspect_ratio' => $width / $height,
            'bit_rate' => intval($video_quality['bit_rate']),
            'final_format' => $final_format
        ];
        if ($height > 1050) {
            $formats = [
                $this->setFormatArray($param, 4, 240),
                $this->setFormatArray($param, 2, 480),
                $this->setFormatArray($param, 4 / 3, 720),
                $this->setFormatArray($param, 1, $height)
            ];
        } elseif ($height > 700) {
            $formats = [
                $this->setFormatArray($param, 4, 240),
                $this->setFormatArray($param, 2, 360),
                $this->setFormatArray($param, 4 / 3, 480),
                $this->setFormatArray($param, 1, $height)
            ];
        } elseif ($height > 450) {
            $formats = [
                $this->setFormatArray($param, 4, 144),
                $this->setFormatArray($param, 2, 240),
                $this->setFormatArray($param, 4 / 3, 360),
                $this->setFormatArray($param, 1, $height)
            ];
        } elseif ($height > 330) {
            $formats = [
                $this->setFormatArray($param, 3, 144),
                $this->setFormatArray($param, 3 / 2, 240),
                $this->setFormatArray($param, 1, $height),
                null
            ];
        } elseif ($height > 210) {
            $formats = [
                $this->setFormatArray($param, 2, 144),
                $this->setFormatArray($param, 1, $height),
                null,
                null
            ];
        } else {
            $formats = [
                $this->setFormatArray($param, 1, $height),
                null,
                null,
                null
            ];
        }
        return $formats;
    }

    /**
     * @Author Amin Yazdanpanah
     * @param $param
     * @param $divide
     * @param $height
     * @return array
     * @internal param $aspect_ratio
     */
    private function setFormatArray($param, $divide, $height)
    {
        $width = intval($height * $param['aspect_ratio']);
        if ($width % 2 == 1) $width++;
        if ($param['final_format']) {
            return [
                'bit_rate' => intval($param['bit_rate'] / $divide),
                'size' => $width . 'x' . $height
            ];
        }
        return [
            'quality' => intval($param['bit_rate'] / $divide),
            'width' => $width,
            'height' => $height,
        ];
    }
}