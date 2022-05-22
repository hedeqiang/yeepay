<?php

namespace Hedeqiang\Yeepay\Util;

/**
 * Created by PhpStorm.
 * User: yp-tc-7176
 * Date: 17/7/16
 * Time: 20:12
 */
abstract class StringUtils
{
    public static function isBlank($field)
    {
        if ($field == '') {
            return false;
        } else {
            return true;
        }
    }
}
