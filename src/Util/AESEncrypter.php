<?php

namespace Hedeqiang\Yeepay\Util;

/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/7
 * Time: 11:07
 */
abstract class AESEncrypter
{
    /**
     * 算法,另外还有192和256两种长度
     */
    public const CIPHER = MCRYPT_RIJNDAEL_128;
    /**
     * 模式
     */
    public const MODE = 'AES-128-ECB';


    /**
     * 加密
     * @param string $str	需加密的字符串
     * @param string $key	密钥
     * @return type
     */


    public static function encode($str, $key)
    {
        return base64_encode(openssl_encrypt($str, self::MODE, base64_decode($key), OPENSSL_RAW_DATA));
    }

    /**
     * 解密
     * @param type $str
     * @param type $key
     * @return type
     */
    public static function decode($str, $key)
    {
        return openssl_decrypt(base64_decode($str), self::MODE, base64_decode($key), OPENSSL_RAW_DATA);
    }
}
