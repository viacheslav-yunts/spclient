<?php
namespace Sap\Odatalib\common;


use Sap\Odatalib\IRequest;
//use Sap\Odatalib\common\OdataConstants;
//use Sap\Odatalib\common\HttpRequestHandler;

/**
 * Class DeliveryBuilder
 * @package Sap\Odatalib\common
 */
class DeliveryBuilder
{
    private   $_http = null;
    protected $_request = null;

    /**
     * DeliveryBuilder constructor.
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $this->_request = $request;
        switch ($this->_request->getRequestType()) {
            case OdataConstants::MULTIPLE :
                // не работает
                $this->_http = new HttpRequestHandler($this->_request);
                break;
            case OdataConstants::SINGLE :
            case OdataConstants::BATCH :
            default :
                $this->_http = new HttpRequestHandler($this->_request);
                break;
        }
    }

    public function checkProxy()
    {
        $this -> _http -> setProxy(false);
    }
    
    public function constructUrl()
    {
        $this -> _http -> setUrl($this -> _request -> constructUrl());
    }
    
    public function constructRequestType()
    {
        $this -> _http -> setRequestType($this -> _request -> getTransferType());
    }

    public function constructHeader()
    {
        $this -> _http -> setHeader($this -> _request -> getHeaderOption());
    }

    public function constructBody()
    {
        $this -> _http -> setBody($this -> _request -> constructBody());
    }

    public function getHttp()
    {
        return $this -> _http;
    }

}