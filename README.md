# PHP Package

易宝支付 SDK for PHP.

## Installing

```shell
$ composer require hedeqiang/yeepay -vvv
```

## Usage
### 下单
```shell
$private_key ="MIIEowIBAAKCAQEAkB8lyP3rcSCJJ4QUM4E4yrmp5ZwAMjR+w5n8H98r6VuonD0wQMjNZFsNk497Nt7QSNSTxZ92ZUVBG3uTmuotP5+hrxX4D1Ylkfik8XmE5vis4z56xaORig24XorscZkg2umB9hZL8BpVDy75gxhgX3TgPz7TeNlAQEQLOcMYdyQ8TVifJLHeqPmEDeeUM6sPdbjrLzbv4Hq0GmbGNTukTmYN3d0ugRLtm+Jqd0OC78PNfgQ+N4YcVHzmc+rHcX4pjSLTeTQTEr3/OLXPAkjfdITAJX9MvgJJ4MOuXnUYGPxVnfB2JlbgIc/+gFCNkv56kPuDotZUGUWB0zr+3isRPwIDAQABAoIBACTCjRSzD3aPYexeV/i7IQ00Rg12uOYVVa/2esR2W/DtnBgd00zy6tCKGPBmTHs2YdHxxq9FeSFjhLPJ1WBSErCDuu25oMfopTSSQyAEas2u6AoeSZpXmiuoTDpxHNXsxOCRZsCp9zyKut9gj+vML+ipGvmGwNG2OBksQeC9zmO0zEKxBbeNe9GCA5iKnSFWM6aC+J4BBrGl0QLle79wHIamR44KRtBfH2BXVgaxsYWDe5NxCATCOl8WdJ19HgNy35Z/vID4Dons5eABzOJs545/5ZO8swMHtIa1cgQaaesSuBKhLtwdnYFA6wpY6iuYI/DgmtlSvu/qNzuqS8jm54ECgYEAyZ5mag54ArzzgRYHMMtA6lOJ26ZCG6xdyAfQJPnzuV9dD56D3xZqorIaFJ7tq0zNWMKed6HGp/dXA6W3tfh8EaGxvh80HuZ7TFg3ClQePmNTF0iqJ7peiJBBOnNAA5lGEv6BII/0g6kGTqsFfyAC/ylHOAVvK8HazJzruZLS5W8CgYEAtv6fvzdhEwJLJ948jpiS/U3xkgBDWhL21v0cEmxpnEvr7DuF1QrhY7svNrHO0y1E+RE2L529BHxBNj2aPEipWCi19BPJ7mJcTtPhgOy+2EU2Af/mIeToT3V6yMyxpjXir5TRYvuy5D0GBar20V3QDohodhlOCZwxUk/UwwJnyTECgYEAqjI/7Al4v37346FPwp3hl62bc0L/DVNM+121FHG3j2V0Loo2cez/aYYFRCEBKpizw9jOYti61PGTj62lzkQLn+qqG39FxUv1C3ZwSBTITwwTiVU65jyKLqfvmELP5/nMUJ5tLKq40yes/l2aTz0bqoAS8bSMqxiC9jUFPGs4ApsCgYAeWrg26MHCfHPpBhU3dhmTyPUUSdTiQRkO6mdYqwENuw5EXk9B2o8ukuMvCGmNYAn74p4BYgHzl4TdsXQ/IbJtfDtKZGnvkANN1Bmo4Bx8FWbzB/atkqHyZENcwY+KSY/znhTpfWTcNT8le4l7izsy3e+t/16Re4Y26CUbv+9lUQKBgCzAiNwyZfMV3B+jtyeBaL8slUAaLqSzyjOmqI+3whsxOmWsnNgiPzvxoDENI8ZCaZyMO6zIjDLOL84u5CcFN0H9H8Ndvq2fS7YTTczCTIfgn/okYvdJxy8OzvviIyOtaLf3RxmW9HWRHfVxO/uLLm6GRiG7GamBDEM3JS+opcID";

	
$request = new YopRequest("app_10085537650", $private_key);


$request->addParam("parentMerchantNo", "10085537650");											
$request->addParam("merchantNo", "10085537650");				
$request->addParam("orderId", "test" . mt_rand(100,999) . mt_rand(100,999). mt_rand(100,999));								
$request->addParam("orderAmount", "0.01");						
$request->addParam("goodsName", "testgoods");						
$request->addParam("notifyUrl", "https://www.momodica.com");												


$response = YopRsaClient::post("/rest/v1.0/trade/order", $request);
```

### 支付回调
```shell
public function notify(Request $request)
{
     $private_key ="MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCrbbD1y4bzxI3f+vPez+41EbrOAnPuIaBZbuwMxnUJ9NxGIyutEf8kcH7zH1jYeFwHhpfINNWz9Q8gkZr8FbmsGcUwB8XEI3QMXBQ8crNcUtO+PJPEAHQyEOyeWI3RQm/W4ebPz3k2zaDAy6Zu2qOHygrK7yWhK4B/QKFxy5FU1OoxcFAED9/pSqGLt+uGDo+7LMK4FdYWmez2vlNvKjiDfSQAWKrXFAaOBAecNFd6PsZ0n+yN/8inoX+QkeJLQjvAISez8gQcRziv1mKR5yM1B0ORwsGQuz1VI06CkK/G/UMQ4+Vj4dz1uxZo+Z4UtrpffTqoEAoc8XoJViiDIGxfAgMBAAECggEAGmbSZ4iTnYdwB5wxYudzMF6CqVJcuG1wB1YQqgcGj3n+qBlSk10U28Us8r9T15hHKYGObsR0WpArZutbNefCqMFvSm5oBEUm4ksWyBiCNyh/zpSrIqH57fFdVSXI8amMap5wBcwA8cfJh5KAbMzk0qJIoeh4SyeAABkxbsKDUiUX20vTAxs8HPObP5CBSTUO/m4SCoVJnLTQuHL4YL+m6QV9U4JDRGgcvT9BjiMhBa6T/aPy3O4QYIM5l3f8yj5baZh9xBd6VH6s1LNXC/QCRfxcecxeL0HhH1nwrVcgVOs23AF/bdIpL7BI/e1eziXRppBOF3a5ye6Y1KVPIiqBQQKBgQDHQGTebAWyAl6YgHfSP1umsaccbX63eJNM4FR9fxs0tP5R5GzAtU0sigJr211zp5vctCtQ6AVrfLA5nIKN0w5YcmHubjMED7O0MIqXlz+P1Mo8tg/izjoxU52CvP8ivx3vPlt/xMTfpsjetBbF2idKZ6iQznbh1tlNmoQ3evgvTQKBgQDcQLGIE3OB5oX3613WNL/1dhVuNOV4ytcklkk25nQjcWKXTv8ncYA2ERgGru64CsityZZ7Bpr/qgFzzznmXsJ0hKKyoR+w80FOkufvhSl7C1Ks4dUGxo+C4LWdDc7jOx4h7V//x6d23rOjOX436UfRDIuRGe7AgSFemo9K3XAMWwKBgA504pyss8EVoO1prMfpZunyS9CpNR90tSNHx3mPhlbNpwIkE6vru5y2qrROpzoj+BAMVnQ5UWNaGaMkcuh9XrYWHnrhLfxmc0BooTWceLUj2ESNZRusoNZBXdVFhzFrMm3QVW0wxqP/guV9pYVXbbBdUwKmqEN8him2Q0+PMYClAoGAMALyMCV9Ul276lUmOWF6TrmU5lclhnVA/Lq97vfBbVB0G2Oe/ywgtKh5Qkuzwe6n3CpLYToJY53dfy83Ad66KMgY5zN0QxBjtgsUAARZDHdlaEY6N6Xk9rShIkE2ThY+9UpXWNxexuy43+XSe8GgZBOGAPVUNCZx7btnbPxkRm0CgYBi/7wOv2BxSx1dGqNlJigRQ7Bfx48A1O/EEVRopKxL2KocrFruT8dQt59WLZpzyPPwqWpKW49OF9m/z3UleL/B0Ruj1szVHN0IB1uVE2DmZduLB9/+3tMJXtZ/U8U1Ui6hAws1hBmDmDUpXaGnDops2ERH5JJCvYExpRyq/zWVRg==";
	$public_key ="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB";


    $response = $request->input('response');

    $response = YopSignUtils::decrypt($response, $private_key , $public_key );

    $response = json_decode($response, true);

    // TODO

    return 'success';

}
```

TODO

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/hedeqiang)


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/hedeqiang/yeepay/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/hedeqiang/yeepay/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
