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
 * Date: 17/7/17
 * Time: 11:42.
 */
class StringBuilder
{
    public const LINE = '<br/>';
    protected $list = [''];

    public function __construct($str = null)
    {
        array_push($this->list, $str);
    }

    public function Append($str)
    {
        array_push($this->list, $str);

        return $this;
    }

    public function AppendLine($str)
    {
        array_push($this->list, $str.self::LINE);

        return $this;
    }

    public function AppendFormat($str, $args)
    {
        array_push($this->list, sprintf($str, $args));

        return $this;
    }

    public function ToString()
    {
        return implode('', $this->list);
    }

    public function __destruct()
    {
        unset($this->list);
    }
}
