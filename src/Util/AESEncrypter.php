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

abstract class AESEncrypter
{
    /**
     * 算法,另外还有192和256两种长度.
     */
    public const CIPHER = MCRYPT_RIJNDAEL_128;
    /**
     * 模式.
     */
    public const MODE = 'AES-128-ECB';

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
        return base64_encode(openssl_encrypt($str, self::MODE, base64_decode($key), OPENSSL_RAW_DATA));
    }

    /***
     * 解密.
     * @param $str
     * @param $key
     * @return false|string
     */
    public static function decode($str, $key)
    {
        return openssl_decrypt(base64_decode($str), self::MODE, base64_decode($key), OPENSSL_RAW_DATA);
    }
}
