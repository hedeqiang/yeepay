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

abstract class HttpUtils
{
    /**
     * Normalize a string for use in url path. The algorithm is:
     * <p>
     * <p>
     * <ol>
     * <li>Normalize the string</li>
     * <li>replace all "%2F" with "/"</li>
     * <li>replace all "//" with "/%2F"</li>
     * </ol>
     * <p>
     * <p>
     * object key can contain arbitrary characters, which may result double slash in the url path. Apache http
     * client will replace "//" in the path with a single '/', which makes the object key incorrect. Thus we replace
     * "//" with "/%2F" here.
     *
     * @param path the path string to normalize
     *
     * @return array|string|string[]
     *
     * @see #normalize(String)
     */
    public static function normalizePath($path)
    {
        return str_replace('%2F', '/', HttpUtils::normalize($path));
    }

    /**
     * @param $value
     *
     * @return string
     */
    public static function normalize($value)
    {
        return rawurlencode($value);
    }

    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return '' === $needle || false !== strrpos($haystack, $needle, -strlen($haystack));
    }

    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return '' === $needle || (($temp = strlen($haystack) - strlen($needle)) >= 0 && false !== strpos($haystack, $needle, $temp));
    }

    /**
     * @param $path
     *
     * @return string
     */
    public static function getCanonicalURIPath($path)
    {
        if (null == $path) {
            return '/';
        } elseif (HttpUtils::startsWith($path, '/')) {
            return HttpUtils::normalizePath($path);
        } else {
            return '/' + HttpUtils::normalizePath($path);
        }
    }
}
