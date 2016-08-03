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

        switch ($this->_request_type) {
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
            throw new \Exception(curl_error($curlHandle));
        } else {
            //$info = curl_getinfo($curlHandle);
            //echo "<pre>"; print_r($info); 

            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            $body = substr($httpResponse, $header_size);
            //echo "<pre>"; print_r($body);

            // grab multipart boundary from content type header
            preg_match('/boundary=(.*)$/', curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE), $matches);
            $boundary = $matches[1];
            echo "<pre>"; print_r($boundary);

            // Fetch each part
            $parts = array_slice(explode($boundary, $body), 1);
            $data = array();

            foreach ($parts as $part) {
                // If this is the last part, break
                if ($part == "--\r\n") break; 

                // Separate content from headers
                $part = ltrim($part, "\r\n");
                echo "<pre>"; print_r(strpos($part,'HTTP/1'));
                echo "<pre>"; print_r(substr($part, strpos($part,'HTTP/1')));
                list($raw_headers, $raw_body) = explode("\r\n\r\n", substr($part, strpos($part,'HTTP/1')), 2);

                // Parse the headers list
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    if (count($arr_header_info = explode(':', $header)) == 2 ) {
                        $headers[strtolower($arr_header_info[0])] = ltrim($arr_header_info[1], ' ');
                    }
                }
                echo "<pre>"; print_r($headers);
                //echo "<pre>"; print_r($raw_body);
                
            }
        }

        //echo "<pre>"; print_r($data);

        curl_close($curlHandle);

        return $httpResponse;
    }
}