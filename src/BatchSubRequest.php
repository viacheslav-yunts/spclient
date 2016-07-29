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
}