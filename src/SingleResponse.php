<?php
namespace Sap\Odatalib;

use Sap\Odatalib\common\ResponseBodyHandler;
class SingleResponse
{
    private $_http_code = 200;
    private $_error  = array();
    private $_headers = array();
    private $_body = '';

    public function __construct($headers, $body)
    {
        $this->_headers = array();

        // Parse the headers list
        $headers = explode("\r\n", $headers);
        foreach ($headers as $header) {
            if (count($arr_header_info = explode(':', $header)) == 2 ) {
                $this->_headers[strtolower($arr_header_info[0])] = ltrim($arr_header_info[1], ' ');
            }
        }

        $this->_body = $body;
    }

    public function getHttpCode()
    {
        return $this->_http_code;
    }

    public function getHeader($header_name)
    {
        $header_name = strtolower(trim($header_name));
        return isset($this->_headers[$header_name])?$this->_headers[$header_name]:FALSE;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function getData()
    {
        return ResponseBodyHandler :: parse($this->getHeader('content-type'), $this->_body);
    }

    public function hasError()
    {
        return count($this->_error);
    }

    public function getMessages()
    {
        return $this->_error;
    }
}