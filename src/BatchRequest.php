<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\SingleRequest;
class BatchRequest extends SingleRequest implements IRequest
{
    public $arr_requests = array();

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