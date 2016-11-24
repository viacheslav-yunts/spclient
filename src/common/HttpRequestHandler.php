<?php 
namespace Sap\Odatalib\common;

require_once('functions.php');
use Sap\Odatalib\common\HttpResponseHandler;
use Sap\Odatalib\config\BaseConfig;
class HttpRequestHandler
{
    use UrlEncodeTrait;

    private $_request_type;
    private $_config;
    private $_url = '';
    private $_http_request_type = '';
    private $_proxy = false;
    private $_headers = false;
    private $_body = false;

    public function __construct($request_type, BaseConfig $connection_config)
    {
        $this->_request_type = $request_type;
        $this->_config = $connection_config;
    }

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function setRequestType($request_type)
    {
        $this->_http_request_type = $request_type;
    }

    public function setProxy($status)
    {
        $this->_proxy = (bool) $status;
    }

    public function setHeader($arr_headers)
    {
        $this->_headers = $arr_headers;
    }

    public function setBody($body)
    {
        $this->_body = $body;
    }

    public function test()
    {
        return array(
            'url'       => $this->_url,
            'header'    => $this->_headers,
            'body'      => (isJson($this->_body))?json_decode($this->_body,TRUE):$this->_body,
            'config'    => $this->_config
        );
    }

    public function execute()
    {

        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $this->url_encode($this->_url)); 
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->_config->getLogin() . ":" . $this->_config->getPassword());
        curl_setopt($curlHandle, CURL_HTTP_VERSION_1_1, true);
        curl_setopt($curlHandle, CURLOPT_COOKIEJAR,  $this->_config->getCookies_path());
        curl_setopt($curlHandle, CURLOPT_COOKIEFILE, $this->_config->getCookies_path());
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        if (! empty($this->_headers)) {
            $headers = array();
            foreach ($this->_headers as $key => $value) {
                $headers[] = $key.': '.$value;
            }
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'ARMTEK USER AGENT');
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $this->_config->getTimeout());
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING , "gzip");

        switch ($this->_http_request_type) {
            case "GET" :
                curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
                break;
            case "POST" :
                curl_setopt($curlHandle, CURLOPT_POST, true);
                curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->_body);
                break;
            default:
                break;
        }

        if (! $httpResponse = curl_exec($curlHandle)) {

            $response = HttpResponseHandler::parse($this->_request_type, 'HTTP/1.0 403 Forbidden', curl_error($curlHandle), '');

        } else {

            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            $response = HttpResponseHandler::parse($this->_request_type, substr($httpResponse, 0, $header_size), substr($httpResponse, $header_size));

        }

        curl_close($curlHandle);

        return $response;
    }
}