<?php
namespace Sap\Odatalib\common;


use Sap\Odatalib\IRequest;
class DeliveryBuilder
{
    private   $_http = null;
    protected $_request = null;

    public function __construct(IRequest $request)
    {
        $this -> _http = new stdClass();
        $this -> _request = $request;
    }

    public function checkProxy()
    {
        $this -> _http -> proxy = false;
    }
    
    public function constructUrl()
    {
        $this -> _http -> url = $this -> _request -> getUrl();
    }

    public function constructHeader()
    {
        $this -> _http -> header = $this -> _request -> getHeaderOption();
    }

    public function constructBody()
    {
        $this -> _http -> body = $this -> _request -> getHeaderOption();
    }

    public function generateCall()
    {
        return $this -> _http;
    }
}