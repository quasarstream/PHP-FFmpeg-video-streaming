<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/23/2018
 * Time: 12:59 AM
 */

namespace App\Library\Format;


use App\Library\Media;

class DASHFormat extends Media
{
    private $format;

    /**
     * DASHFormat constructor.
     * @param $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * @param $bitrate
     * @param $width
     * @param $height
     */
    public function add($bitrate, $width, $height)
    {

    }
}