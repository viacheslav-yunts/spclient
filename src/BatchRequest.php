<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\SingleRequest;
use Sap\Odatalib\BatchSubRequest;
class BatchRequest extends SingleRequest implements IRequest
{
    public $_requests = array();

    public function add(BatchSubRequest $single_request, $key = null) 
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
    
    public function constructBody()
    {
        $request_body = '';

        foreach ($this->_requests as $request) {

            $request_body .= "\n--batch\n";
            if ($request->getRequestType() == 'POST') {
                $request_body .= "\nContent-Type: multipart/mixed; boundary=changeset\n";
                $request_body .= "\n--changeset\n";
                
            }
            $request_body .= "\nContent-Type: application/http\n";
            $request_body .= "\nContent-Transfer-Encoding: binary\n";

            $request_body .= "\n" . $request->getRequestType() . " " . $request->getUrl() . " HTTP/1.1\n";
    
            if ($request->getRequestType() == 'POST') {
                $request_body .= "\n Content-Type: application/json; charset=utf-8 \n";
                $request_body .= "\n Accept: application/json \n";
                $request_body .= "\n" . json_encode($request->getParams()) . "\n";
                $request_body .= "\n--changeset--\n";
                
            }
        }

        $request_body .= "\n--batch--\n";
        return $request_body;
    }
    
    
    
    
    
    /**
    * function buildQuery()
    * 
    * @return string $query
    */
    public function buildQuery()
    {

        return $this;
        
        $httpRequest = $this->_initRequest();
        
        $query = $httpRequest->buildQuery();
        
        //echo "<pre>"; print_r( $query ); echo "</pre>"; die;
        
        return $query;
    }

    public function execute()
    {
        
    }
}