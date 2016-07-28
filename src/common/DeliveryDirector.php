<?php
namespace Sap\Odatalib\common;


use Sap\Odatalib\common\DeliveryBuilder;
class DeliveryDirector
{
    protected $_builder = null;

    public function __construct(DeliveryBuilder $builder)
    {
        $this -> _builder = $builder;
    }

    public function constructRequest()
    {
        $this -> _builder -> constructUrl();
        $this -> _builder -> checkProxy();
        $this -> _builder -> constructHeader();
        $this -> _builder -> constructBody();
    }
}