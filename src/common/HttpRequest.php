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
            echo "<pre>"; print_r($body);

            // grab multipart boundary from content type header
            preg_match('/boundary=(.*)$/', curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE), $matches);
            $boundary = $matches[1];
            echo "<pre>"; print_r($boundary);

            // split content by boundary and get rid of last -- element
            $a_blocks = preg_split("/-+$boundary/", $body);
            array_pop($a_blocks);

            // loop data blocks
            foreach ($a_blocks as $id => $block) {
                if (empty($block)) continue;

                // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

                // parse uploaded files
                if (strpos($block, 'application/octet-stream') !== FALSE) {
                    // match "name", then everything after "stream" (optional) except for prepending newlines 
                    preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                } else {
                    // match "name" and optional value in between newline sequences
                    preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                }

                $a_data[$matches[1]] = $matches[2];
            }
            echo "<pre>"; print_r($a_data);
        }

        curl_close($curlHandle);

        return $httpResponse;
    }
}