<?php 
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\SingleRequest;
class MultiRequest implements IRequest
{
    private $_requests = array();

    public function add(SingleRequest $single_request, $key = null) 
    {
        if ($key == null) {
            $this->_requests[] = $single_request;
        } else {
            if (isset($this->_requests[$key])) {
                throw new \Exception("Key $key already in use.");
            } else {
                $this->_requests[$key] = $single_request;
            }
        }
    }

    public function delete($key) 
    {
        if (isset($this->_requests[$key])) {
            unset($this->_requests[$key]);
        } else {
            throw new \Exception("Invalid key $key.");
        }
    }

    public function get($key)
    {
        if (isset($this->_requests[$key])) {
            return $this->_requests[$key];
        } else {
            throw new \Exception("Invalid key $key.");
        }
    }

    public function keyExists($key) {
        return isset($this->_requests[$key]);
    }

    /**
    * function buildQuery()
    * 
    * @return string $query
    */
    public function buildQuery()
    {
        return true;
    }
    
    public function execute()
    {
        
    }
    
    
    public function getConfig()
    {
        return false;
    }

    public function constructUrl()
    {
        return '';
    }
    
    public function constructBody()
    {
        return '';
    }
    public function getHeaderOption()
    {
        return '';
    }
}