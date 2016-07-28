<?php 
namespace Sap\Odatalib\common;


//use Sap\Odatalib\common\UrlEncodeTrait;
use Sap\Odatalib\config\BaseSapConfig;
class HttpRequest
{
    //use Sap\Odatalib\common\UrlEncodeTrait;

    private $_config;
    private $_url = '';
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
        return true;
    }
}