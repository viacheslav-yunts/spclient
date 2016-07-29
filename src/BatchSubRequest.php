<?php
namespace Sap\Odatalib;


//use Sap\Odatalib\IRequest;
//use Sap\Odatalib\SingleRequest;
class BatchSubRequest
{
    private $_url = null;
    private $_req_type = null;
    
    public function __construct($method_name, $method_request_type)
    {
        $this->_url = $method_name;
        $this->_req_type = $method_request_type;
    }

    public function getUrl()
    {
        return $this->_url;
    }
    
    public function getRequestType()
    {
        return $this->_req_type;
    }
    // добавляем переменную в запрос
    public function addParam($param_name, $param_value, $wrap_in_quotes =false )
    {
        if ($wrap_in_quotes) $param_value = "'".$param_value."'";
        $this->params[$param_name] = $param_value;
        //$this->AddQueryOption($param_name, $param_value );
    }
    
    public function getParams()
    {
        return $this->params;
    }
}