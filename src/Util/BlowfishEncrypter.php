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

abstract class BlowfishEncrypter
{
    /**
     * 算法,另外还有192和256两种长度.
     */
    public const CIPHER = MCRYPT_BLOWFISH;
    /**
     * 模式.
     */
    public const MODE = MCRYPT_MODE_CFB;

    /**
     * 加密.
     *
     * @param string $str 需加密的字符串
     * @param string $key 密钥
     *
     * @return string
     */
    public static function encode($str, $key)
    {
        echo $str;
        echo '123456789';
        echo '<br>';
        echo $key;
        $md5Key = md5($key);

        return base64_encode(mcrypt_encrypt(self::CIPHER, substr($md5Key, 0, 16), $str, self::MODE, substr($md5Key, 0, 8)));
    }

    /***
     * 解密.
     * @param $str
     * @param $key
     * @return string
     */
    public static function decode($str, $key)
    {
        $md5Key = md5($key);

        return mcrypt_decrypt(self::CIPHER, substr($md5Key, 0, 16), base64_decode($str), self::MODE, substr($md5Key, 0, 8));
    }
}
