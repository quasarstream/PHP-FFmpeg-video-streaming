<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/21/2018
 * Time: 10:57 PM
 */

namespace App\Library;


use App\Library\Format\DASHFormat;

class Initialize
{
    private $video;

    /**
     * Initialize constructor.
     * @param $video
     */
    public function __construct($video)
    {
        $this->video = $video;
    }


    public function autoFormat()
    {

    }

    public function addFormat(DASHFormat $format)
    {

    }

    public function export()
    {
        return new MediaExport();
    }

}