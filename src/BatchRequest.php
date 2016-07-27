<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\SingleRequest;
class BatchRequest extends SingleRequest implements IRequest
{
    public $arr_requests = array();

    public function execute()
    {
        
    }
}