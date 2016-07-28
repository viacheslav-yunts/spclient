<?php
namespace Sap\Odatalib\Common;


use Sap\Odatalib\Common\DeliveryBuilder;
class DeliveryDirector
{
    protected $_builder = null;

    public function __construct(DeliveryBuilder $request)
    {
        $this -> _builder = $request;
    }

    public function constructRequest()
    {
        $this -> _builder -> constructUrl();
        $this -> _builder -> checkProxy();
        $this -> _builder -> constructHeader();
        $this -> _builder -> constructBody();
    }
}