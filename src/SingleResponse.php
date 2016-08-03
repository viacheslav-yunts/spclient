<?php
namespace Sap\Odatalib;

use Sap\Odatalib\common\ResponseBodyHandler;
class SingleResponse
{
    private $_http_code = 200;
    private $_http_text = '';
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
            } else {

                $this->_headers['http_code'] = $header;

                preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
                if ( isset($matches[1])) {
                    $this->_http_code = $matches[1];
                    $this->_http_text = trim(str_replace($matches[0], '', $header));
                }

            }
        }

        $this->_body = $body;
    }

    public function getHttpCode()
    {
        return $this->_http_code;
    }

    public function getHttpCodeText()
    {
        return $this->_http_text;
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