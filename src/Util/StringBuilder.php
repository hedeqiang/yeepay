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

class StringBuilder
{
    public const LINE = '<br/>';
    protected $list = [''];

    public function __construct($str = null)
    {
        $this->list[] = $str;
    }

    public function Append($str)
    {
        $this->list[] = $str;

        return $this;
    }

    public function AppendLine($str)
    {
        $this->list[] = $str.self::LINE;

        return $this;
    }

    public function AppendFormat($str, $args)
    {
        $this->list[] = sprintf($str, $args);

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
