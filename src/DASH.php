<?php
/**
 * Created by PhpStorm.
 * User: amin6
 * Date: 10/21/2018
 * Time: 10:52 PM
 */

namespace App\Library;


class DASH
{
    public static function input($video)
    {
        return new Initialize($video);
    }
}