<?php

/*
 * This file is part of the hedeqiang/yeepay
 *
 * (c) hedeqiang <laravel_code@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hedeqiang\Yeepay;

use Hedeqiang\Yeepay\Util\HttpRequest;
use Hedeqiang\Yeepay\Util\HttpUtils;
use Hedeqiang\Yeepay\Util\StringUtils;
use Hedeqiang\Yeepay\Util\Base64Url;

class YopRsaClient
{
    public function __construct()
    {
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     *
     * @return array
     */
    public static function SignRsaParameter($methodOrUri, $YopRequest)
    {
        $appKey = $YopRequest->{$YopRequest->config->APP_KEY};
        if (empty($appKey)) {
            $appKey = $YopRequest->config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->config->APP_KEY);
        }
        if (empty($appKey)) {
            error_log('appKey 与 customerNo 不能同时为空');
        }

        date_default_timezone_set('PRC');
        $dataTime = new \DateTime();
        $timestamp = $dataTime->format(\DateTime::ISO8601); // Works the same since const ISO8601 = "Y-m-d\TH:i:sO"

        $headers = [];

        $headers['x-yop-appkey'] = $YopRequest->appKey;
        $headers['x-yop-request-id'] = $YopRequest->requestId;

        $protocolVersion = 'yop-auth-v2';
        $EXPIRED_SECONDS = '1800';

        $authString = $protocolVersion.'/'.$appKey.'/'.$timestamp.'/'.$EXPIRED_SECONDS;

        $headersToSignSet = [];
        $headersToSignSet[] = 'x-yop-request-id';

        $appKey = $YopRequest->{$YopRequest->config->APP_KEY};

        if (!StringUtils::isBlank($YopRequest->config->CUSTOMER_NO)) {
            $headers['x-yop-customerid'] = $appKey;
            $headersToSignSet[] = 'x-yop-customerid';
        }

        // Formatting the URL with signing protocol.
        $canonicalURI = HttpUtils::getCanonicalURIPath($methodOrUri);

        // Formatting the query string with signing protocol.
        $canonicalQueryString = YopRsaClient::getCanonicalQueryString($YopRequest, true);

        // Sorted the headers should be signed from the request.
        $headersToSign = YopRsaClient::getHeadersToSign($headers, $headersToSignSet);

        // Formatting the headers from the request based on signing protocol.
        $canonicalHeader = YopRsaClient::getCanonicalHeaders($headersToSign);

        $signedHeaders = '';
        if (null != $headersToSignSet) {
            foreach ($headersToSign as $key => $value) {
                $signedHeaders .= 0 == strlen($signedHeaders) ? '' : ';';
                $signedHeaders .= $key;
            }
            $signedHeaders = strtolower($signedHeaders);
        }

        $canonicalRequest = $authString."\n".$YopRequest->httpMethod."\n".$canonicalURI."\n".$canonicalQueryString."\n".$canonicalHeader;

        // Signing the canonical request using key with sha-256 algorithm.

        if (empty($YopRequest->secretKey)) {
            error_log('secretKey must be specified');
        }

        extension_loaded('openssl') or exit('php需要openssl扩展支持');

        $private_key = $YopRequest->secretKey;
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n".
            wordwrap($private_key, 64, "\n", true).
            "\n-----END RSA PRIVATE KEY-----";
        $privateKey = openssl_pkey_get_private($private_key); // 提取私钥
        ($privateKey) or exit('密钥不可用');

        $signToBase64 = '';
        // echo "tyuiop".$canonicalRequest;
        openssl_sign($canonicalRequest, $encode_data, $privateKey, 'SHA256');

        // openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);

        $signToBase64 .= '$SHA256';

        $headers['Authorization'] = 'YOP-RSA2048-SHA256 '.$protocolVersion.'/'.$appKey.'/'.$timestamp.'/'.$EXPIRED_SECONDS.'/'.$signedHeaders.'/'.$signToBase64;

        if ($YopRequest->config->debug) {
            var_dump('authString='.$authString);
            var_dump('canonicalURI='.$canonicalURI);
            var_dump('canonicalQueryString='.$canonicalQueryString);
            var_dump('canonicalHeader='.$canonicalHeader);
            var_dump('canonicalRequest='.$canonicalRequest);
            var_dump('signToBase64='.$signToBase64);
        }
        $YopRequest->headers = $headers;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function get($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::getForString($methodOrUri, $YopRequest);
        return YopRsaClient::handleRsaResult($YopRequest, $content);
    }

    public static function getForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = 'GET';
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);
        $serverUrl .= (false === strpos($serverUrl, '?') ? '?' : '&').$YopRequest->toQueryString();

        self::SignRsaParameter($methodOrUri, $YopRequest);
        return HttpRequest::curl_request($serverUrl, $YopRequest);
    }

    public static function post($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::postString($methodOrUri, $YopRequest);
        return YopRsaClient::handleRsaResult($YopRequest, $content);
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return array|Util\type
     */
    public static function postString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = 'POST';
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);

        self::SignRsaParameter($methodOrUri, $YopRequest);
        return HttpRequest::curl_request($serverUrl, $YopRequest);
    }

    /**
     * @param $YopRequest
     * @param $forSignature
     *
     * @return string
     */
    public static function getCanonicalQueryString($YopRequest, $forSignature)
    {
        if (!empty($YopRequest->jsonParam)) {
            return '';
        }

        $ArrayList = [];
        $StrQuery = '';
        foreach ($YopRequest->paramMap as $k => $v) {
            if ($forSignature && 0 == strcasecmp($k, 'Authorization')) {
                continue;
            }
            $ArrayList[] = $k . '=' . rawurlencode($v);
        }
        sort($ArrayList);

        foreach ($ArrayList as $kv) {
            $StrQuery .= 0 == strlen($StrQuery) ? '' : '&';
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $headers
     * @param $headersToSign
     *
     * @return array
     */
    public static function getHeadersToSign($headers, $headersToSign)
    {
        $ret = [];
        if (null != $headersToSign) {
            $tempSet = [];
            foreach ($headersToSign as $header) {
                $tempSet[] = strtolower(trim($header));
            }

            $headersToSign = $tempSet;
        }

        foreach ($headers as $key => $value) {
            if (!empty($value)) {
                if ((null == $headersToSign && self::isDefaultHeaderToSign($key)) || (null != $headersToSign && in_array(strtolower($key), $headersToSign) && 'Authorization' != $key)) {
                    $ret[$key] = $value;
                }
            }
        }
        ksort($ret);

        return $ret;
    }

    /**
     * @param $header
     *
     * @return bool
     */
    public static function isDefaultHeaderToSign($header)
    {
        $header = strtolower(trim($header));
        $defaultHeadersToSign = [];
        $defaultHeadersToSign[] = 'host';
        $defaultHeadersToSign[] = 'content-type';

        return 0 == strpos($header, 'x-yop-') || in_array($defaultHeadersToSign, $header);
    }

    /**
     * @param $headers
     *
     * @return string
     */
    public static function getCanonicalHeaders($headers)
    {
        if (empty($headers)) {
            return '';
        }

        $headerStrings = [];

        foreach ($headers as $key => $value) {
            if (null == $key) {
                continue;
            }
            if (null == $value) {
                $value = '';
            }
            $key = HttpUtils::normalize(strtolower(trim($key)));
            $value = HttpUtils::normalize(trim($value));
            $headerStrings[] = $key . ':' . $value;
        }

        sort($headerStrings);
        $StrQuery = '';

        foreach ($headerStrings as $kv) {
            $StrQuery .= 0 == strlen($StrQuery) ? '' : "\n";
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     *
     * @return YopResponse
     */
    public static function upload($methodOrUri, $YopRequest)
    {
        $content = self::uploadForString($methodOrUri, $YopRequest);
        $response = self::handleRsaResult($YopRequest, $content);

        return $response;
    }

    public static function uploadForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = 'POST';
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);
        self::SignRsaParameter($methodOrUri, $YopRequest);
        return HttpRequest::curl_request($serverUrl, $YopRequest);
    }

    public static function richRequest($methodOrUri, $YopRequest)
    {
        if (strpos($methodOrUri, $YopRequest->config->serverRoot)) {
            $methodOrUri = substr($methodOrUri, strlen($YopRequest->config->serverRoot) + 1);
        }
        $serverUrl = $YopRequest->serverRoot;
        $serverUrl .= $methodOrUri;
        preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
        if (!empty($version)) {
            $version = $version[1];
            if (!empty($version)) {
                $YopRequest->setVersion($version);
            }
        }
        $YopRequest->setMethod($methodOrUri);

        return $serverUrl;
    }

    public static function handleRsaResult($YopRequest, $content)
    {
        $sign = trim($content['header']['x-yop-sign']);
        $signStr = $content['content'];
        $signStr = self::trimall($signStr);
        $response = new YopResponse();
        $jsoncontent = json_decode($content['content']);

        if (empty($sign)) {
            return $content['content'];
        }

        if (!empty($jsoncontent->result)) {
            $response->state = 'SUCCESS';
            $response->result = $jsoncontent->result;
            $response->requestId = $YopRequest->requestId;
        // $signStr=$jsoncontent->result;
        } else {
            $response->state = 'FAILURE';
            $response->requestId = $jsoncontent->requestId;
            $response->error->code = $jsoncontent->code;
            $response->error->message = $jsoncontent->message;
            $response->error->subCode = $jsoncontent->subCode;
            $response->error->subMessage = $jsoncontent->subMessage;
//            $signStr = $content['content'];
        }
        $response->validSign = YopRsaClient::isValidRsaResult($signStr, $sign, $YopRequest->yopPublicKey);

        return $response;
    }

    // 去空格换行符
    public static function trimall($str)
    {
        $qian = [' ', '　', "\t", "\n", "\r"];

        return str_replace($qian, '', $str);
    }

    // header sign 验签
    public static function isValidRsaResult($result, $sign, $public_key)
    {
        // $result=json_encode($result,320);
        $str = '';
        if (empty($result)) {
            $str = '';
        } else {
            $str .= trim($result);
        }

        $public_key = "-----BEGIN PUBLIC KEY-----\n".
            wordwrap($public_key, 64, "\n", true).
            "\n-----END PUBLIC KEY-----";
        $pu_key = openssl_pkey_get_public($public_key);

        //  $str=str_replace("\\","",str_replace("\\n","",$str));

        $str = self::trimall($str);
        $str = trim($str, '"');

        $res = openssl_verify($str, Base64Url::decode($sign), $pu_key, 'SHA256'); // 验证
        // openssl_free_key($pu_key);
        if (1 == $res) {
            return true;
        } else {
            return false;
        }
    }
}
