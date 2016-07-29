<?php
namespace Sap\Odatalib\common;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\common\HttpRequest;
class DeliveryBuilder
{
    private   $_http = null;
    protected $_request = null;

    public function __construct(IRequest $request)
    {
        $this -> _request = $request;
        $this -> _http = new HttpRequest($this -> _request -> getConfig());
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