<?php

namespace Hedeqiang\Yeepay;

class YopRequest
{
    public $config;

    public $httpMethod;
    public $method;
    public $version = "2.0";
    public $signAlg = "sha256";

    /**
     * 商户编号，易宝商户可不注册开放应用(获取appKey)也可直接调用API
     */
    public $customerNo;

    public $headers = array();
    public $paramMap = array();
    public $fileMap = array();
    public $jsonParam;
    public $ignoreSignParams = array('sign');

    public $requestId;

    /**
     * 连接超时时间
     */
    public $connectTimeout = 30000;

    /**
     * 读取返回结果超时
     */
    public $readTimeout = 60000;

    /**
     * 可支持不同请求使用不同的appKey及secretKey
     */
    public $appKey;


    /**
     * 报文是否加密，如果请求加密，则响应也加密，需做解密处理
     */
    public $encrypt = false;
    /**
     * 可支持不同请求使用不同的appKey及secretKey,secretKey只用于本地签名，不会被提交
     */
    public $secretKey;

    /**
     * 可支持不同请求使用不同的appKey及secretKey、serverRoot,secretKey只用于本地签名，不会被提交
     */
    public $yopPublicKey;

    /**
     * 业务结果是否签名，默认不签名
     */
    public $signRet = false;
    /**


    /**
     * 可支持不同请求使用不同的appKey及secretKey、serverRoot,secretKey只用于本地签名，不会被提交
     */
    public $serverRoot;
    public $downrequest;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
    public function __get($name)
    {
        return $this->$name;
    }

    public function setSignRet($signRet)
    {
        $signRetStr = $signRet ? 'true' : 'false';
        $this->signRet = $signRet;
        $this->addParam($this->Config->SIGN_RETURN, $signRetStr);
    }

    public function setSignAlg($signAlg)
    {
        $this->signAlg = $signAlg;
    }

    public function setEncrypt($encrypt)
    {
        $this->encrypt = $encrypt;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function __construct($appKey = '', $secretKey = null, $yopPublicKey = null, $serverRoot = null)
    { //定义构造函数
        $this->config = new YopConfig();
        $this->requestId = YopRequest::uuid();

        if (!empty($appKey)) {
            $this->appKey = $appKey;
        } else {
            $this->appKey = $this->config->appKey;
        }
        if (!empty($secretKey)) {
            $this->secretKey = $secretKey;
        } else {
            $this->secretKey = $this->config->getSecret();
        }

        if (!empty($yopPublicKey)) {
            $this->yopPublicKey = $yopPublicKey;
        } else {
            $this->yopPublicKey = $this->config->publickey;
        }

        if (!empty($serverRoot)) {
            $this->serverRoot = $serverRoot;
        } else {
            $this->serverRoot = $this->config->serverRoot;
        }
    }

    public function addParam($key, $values)
    {
        if ("_file" == $key) {
            YopRequest::addFile($key, $values);
        } else {
            $addParam = array($key => $values);
            $this->paramMap = array_merge($this->paramMap, $addParam);
        }
    }

    public function addFile($key, $values)
    {
        $this->ignoreSignParams = array_push($this->ignoreSignParams, $key);
        $addFile = array($key => $values);
        $this->fileMap = array_merge($this->fileMap, $addFile);
    }

    public function removeParam($key)
    {
        foreach ($this->paramMap as $k => $v) {
            if ($key == $k) {
                unset($this->paramMap[$k]);
            }
        }
    }

    public function getParam($key)
    {
        return $this->paramMap[$key];
    }

    public function setJsonParam($jsonParam)
    {
        $this->jsonParam = $jsonParam;
    }

    public function getJsonParam()
    {
        return $this->jsonParam;
    }

    public function encoding()
    {
        foreach ($this->paramMap as $k => $v) {
            $this->paramMap[$k] = urlencode($v);
        }
    }

    /**
     * 将参数转换成k=v拼接的形式
     */
    public function toQueryString()
    {
        $StrQuery = "";
        foreach ($this->paramMap as $k => $v) {
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "&";
            $StrQuery .= $k."=".urlencode($v);
        }
        return $StrQuery;
    }

    private function uuid($namespace = '')
    {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $_SERVER['REQUEST_TIME'];
        $hash = hash('ripemd128', $uid . $data);

        $guid = $namespace .
                substr($uid, 0, 14) .
                substr($uid, 15, 24) .
                substr($hash, 0, 10) .
                '';
        return $guid;
    }
}
