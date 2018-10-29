<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/23/2018
 * Time: 1:16 AM
 */

namespace App\Library;


interface MediaInterface
{
    public function ffprobe();

    public function ffmpeg();
}