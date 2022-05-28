<?php

namespace Hedeqiang\Yeepay;

use GuzzleHttp\Client;
use Hedeqiang\Yeepay\Support\Config;

class Pay
{
    protected $config;


    protected $guzzleOptions = [];

    const ENDPOINT_TEMPLATE = 'https://openapi.yeepay.com/yop-center/%s';
    const ENDPOINT_UPLOAD_TEMPLATE = 'https://yos.yeepay.com/yop-center/%s';
    const PROTOCOL_VERSION = 'yop-auth-v3';


    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    protected function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }


    protected function getHeaders($uri, $params)
    {
        $headers = [];
        $appKey = $this->config->get('appKey');
        $headers['x-yop-appkey'] = $appKey;
        $headers['x-yop-request-id'] = $params['requestId'];

        date_default_timezone_set('PRC');
        $dataTime = new \DateTime();
        $timestamp = $dataTime->format(\DateTime::ISO8601);

        $expired_seconds = '1800';
        $authStr = self::PROTOCOL_VERSION . '/' . $appKey . '/' . $timestamp . '/' . $expired_seconds;

        $httpRequestMethod = 'POST';

        
        $canonicalRequest = $authStr . '\n' . $httpRequestMethod . '\n' . $uri . '\n';
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
            $key = rawurlencode(strtolower(trim($key)));
            $value = rawurlencode(trim($value));
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

}
