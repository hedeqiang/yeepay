<?php

/*
 * This file is part of the hedeqiang/yeepay
 *
 * (c) hedeqiang <laravel_code@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hedeqiang\Yeepay\Util;

/**
 * Created by PhpStorm.
 * User: yp-tc-7176
 * Date: 17/7/16
 * Time: 20:12.
 */
abstract class StringUtils
{
    public static function isBlank($field)
    {
        if ('' == $field) {
            return false;
        } else {
            return true;
        }
    }
}
