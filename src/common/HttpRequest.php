<?php 
namespace Sap\Odatalib\common;


//use Sap\Odatalib\common\UrlEncodeTrait;
use Sap\Odatalib\config\BaseSapConfig;
class HttpRequest
{
    use UrlEncodeTrait;

    private $_config;
    private $_url = '';
    private $_request_type = '';
    private $_proxy = false;
    private $_headers = false;
    private $_body = false;

    public function __construct(BaseSapConfig $connection_config)
    {
        $this->_config = $connection_config;
    }

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function setRequestType($request_type)
    {
        $this->_request_type = $request_type;
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
            'body'      => $this->_body,
            'config'    => $this->_config
        );
    }

    public function execute()
    {
        $curlHandle = curl_init();
        
        curl_setopt($curlHandle, CURLOPT_URL, $this->url_encode($this->_url)); 
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->_config->getLogin() . ":" . $this->_config->getPassword());
        curl_setopt($curlHandle, CURL_HTTP_VERSION_1_1, true);
        //curl_setopt($curlHandle, CURLOPT_COOKIEJAR,  $this->_config->getCookiesFilePath());
        //curl_setopt($curlHandle, CURLOPT_COOKIEFILE, $this->_config->getCookiesFilePath());
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

        switch ($this->_request_type) {
            case "GET" :
                curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
                break;
            case "POST" :
                curl_setopt($curlHandle, CURLOPT_POST, true);
                curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($this->_body));
                break;
            default:
                break;
        }

        if (! $httpResponse = curl_exec($curlHandle)) {
            trigger_error(curl_error($curlHandle));
        } else {
            $info = curl_getinfo($curlHandle);
            echo "<pre>"; print_r($info); 
            
            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            $body = substr($httpResponse, $header_size);
            echo "<pre>"; print_r($body);
        
        } 

        curl_close($curlHandle);

        return $httpResponse;
    }
}