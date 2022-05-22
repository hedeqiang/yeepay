<?php

namespace Hedeqiang\Yeepay\Util;

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
define("LANGS", "php");
define("VERSION", "3.2.11");
define("USERAGENT", LANGS."/".VERSION."/".PHP_OS."/".$_SERVER ['SERVER_SOFTWARE']."/Zend Framework/".zend_version()."/".PHP_VERSION."/".$_SERVER['HTTP_ACCEPT_LANGUAGE']."/");

abstract class HTTPRequest
{
    /**
     * 加密
     * @param string $str	    需加密的字符串
     * @param string $key	    密钥
     * @param string $CIPHER	算法
     * @param string $MODE	    模式
     * @return type
     */
    public static function curl_request($url, $request)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, USERAGENT);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $request->readTimeout);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $request->connectTimeout);

        $TLS = substr($url, 0, 8) == "https://" ? true : false;
        if ($TLS) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $request->encoding();

        $headerArray = array();
        if ($request->headers != null) {
            foreach ($request->headers as  $key => $value) {
                array_push($headerArray, $key.":".$value);
            }
        }
        array_push($headerArray, "x-yop-sdk-langs:".LANGS);
        array_push($headerArray, "x-yop-sdk-version:".VERSION);
        array_push($headerArray, "x-yop-request-id:".$request->requestId);
        if ($request->jsonParam != null) {
            array_push(
                $headerArray,
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($request->jsonParam)
            );
        }

        var_dump($headerArray);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        //curl_setopt($curl, CURLINFO_HEADER_OUT, );


        //var_dump($request);
        //var_dump($request->httpMethod);

        if ("POST" == $request->httpMethod) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($request->jsonParam != null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->jsonParam);
            } else {
                $fields = $request->paramMap;
                if ($request->fileMap) {
                    foreach ($request->fileMap as $fileParam => $fileName) {
                        //$file_name = str_replace("%2F", "/",$post["_file"]);
                        //var_dump($fileParam);
                        //var_dump($fileName);
                        //var_dump($file_name);

                        // 从php5.5开始,反对使用"@"前缀方式上传,可以使用CURLFile替代;
                        // 据说php5.6开始移除了"@"前缀上传的方式
                        if (class_exists('CURLFile')) {
                            // 禁用"@"上传方法,这样就可以安全的传输"@"开头的参数值
                            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
                            $file = new CURLFile($fileName);
                        } else {
                            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
                            $file = "@{$fileName}";
                        }

                        $fields [$fileParam] = $file;
                    }
                    curl_setopt($curl, CURLOPT_INFILESIZE, $request->config->maxUploadLimit);
                    curl_setopt($curl, CURLOPT_BUFFERSIZE, 128);
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            }
        } else {
            curl_setopt($curl, CURLOPT_URL, $url);
        }
        $data = curl_exec($curl);
        var_dump($data);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        $info['code'] = $httpCode;
        if (true) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            $headers = explode("\r\n", $header);
            $headList = array();
            foreach ($headers as $head) {
                $value = explode(':', $head);
                $headList[$value[0]] = $value[1];
            }

            $bodys = explode("\r\n", $body);
            foreach ($bodys as $body) {
                $value = explode(':', $body);
                $headList[$value[0]] = $value[1];
            }

            $info['header'] = $headList;
//            print_r($headList);
//            echo '----------<br>';
            $info['content'] = $body;
//            print_r($body);
            return $info;
        } else {
            $info['content'] = $data;
        }
        curl_close($curl);
        return $data;
    }
}
