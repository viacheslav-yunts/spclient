<?php
namespace Sap\Odatalib;


class Response
{
    private $_http_code = 200;
    private $_error  = array();
    private $_header = '';
    private $_body = '';

    public function __construct($header, $body)
    {
        $this->_header = $header;
        $this->_body = $body;
    }

    public function getHttpCode()
    {
        return $this->_http_code;
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function hasErrors()
    {
        return count($this->_error);
    }

    public function getMessages()
    {
        return $this->_error;
    }
}