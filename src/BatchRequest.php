<?php
namespace Sap\Odatalib;


use Sap\Odatalib\common\OdataConstants;
//use Sap\Odatalib\IRequest;
//use Sap\Odatalib\SingleRequest;
//use Sap\Odatalib\BatchSubRequest;

/**
 * Class BatchRequest
 * @package Sap\Odatalib
 */
class BatchRequest extends SingleRequest implements IRequest
{
    /**
     * @var string
     */
    protected $_type = OdataConstants::BATCH;

    /**
     * @var array
     */
    public $_requests = [];

    /**
     * @param \Sap\Odatalib\BatchSubRequest $single_request
     * @param null $key
     * @throws \Exception
     */
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

    /**
     * @param $key
     * @throws \Exception
     */
    public function delete($key) 
    {
        if (isset($this->_requests[$key])) {
            unset($this->_requests[$key]);
        } else {
            throw new \Exception("Invalid key $key.");
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        if (isset($this->_requests[$key])) {
            return $this->_requests[$key];
        } else {
            throw new \Exception("Invalid key $key.");
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function keyExists($key) {
        return isset($this->_requests[$key]);
    }

    /**
     * @return string
     */
    public function constructBody()
    {
        $request_body = '';

        foreach ($this->_requests as $request) {

            $request_body .= "--batch\r\n";
            if ($request->getRequestType() == 'POST') {
                $request_body .= "Content-Type: multipart/mixed; boundary=changeset\r\n";
                $request_body .= "\r\n--changeset\r\n";
                
            }
            $request_body .= "Content-Type: application/http\r\n";
            $request_body .= "Content-Transfer-Encoding: binary\r\n";

            $request_body .= "\r\n" . $request->getRequestType() . " " . $request->getUrl() . " HTTP/1.1\r\n";
    
            if ($request->getRequestType() == 'POST') {
                $json = json_encode($request->getParams());
                $request_body .= "Content-Type: application/json; charset=utf-8 \r\n";
                $request_body .= "Content-Length: " . strlen($json) . "\r\n";
                $request_body .= "Accept: application/json \r\n";
                $request_body .= "\r\n" . $json . "\r\n";
                $request_body .= "--changeset--\r\n";
            }
            $request_body .= "\r\n\r\n";
        }

        $request_body .= "--batch--\r\n";

        return $request_body;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->_type;
    }

    /**
     * @return $this
     */
    public function buildQuery()
    {

        return $this;
        /*
        $httpRequest = $this->_initRequest();
        
        $query = $httpRequest->buildQuery();
        
        //echo "<pre>"; print_r( $query ); echo "</pre>"; die;
        
        return $query;
        */
    }

    /**
     * @return $this
     */
    public function execute()
    {
        return $this;
    }
}