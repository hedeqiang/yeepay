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
