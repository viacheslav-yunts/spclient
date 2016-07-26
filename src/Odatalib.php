<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
/**
* 
*/
class Odatalib 
{
    private $_http_request = null;
    private $_request = null;

    public function getRequest()
    {
        return $this->_request;
    }

    public function setRequest(IRequest $request)
    {
        $this->_request = $request;
    }

    public function setResponce()
    {
        
    }

    public function getResponce()
    {
        
    }

    public function buildQuery()
    {
        $this->_createHttpRequest();
        
    }

    /**
    * выполнить запрос
    */
    public function execute()
    {
        $this->_createHttpRequest();
        return true;
    }
    
    private function _createHttpRequest()
    {
        if (! is_null($this->getRequest())) {
            $this->_http_request = new HttpRequest(
                $this->getRequest(), $this->getConfig()
            );
        }
    }
}