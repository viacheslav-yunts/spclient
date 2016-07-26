<?php 
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
class MultiRequest implements IRequst
{
    private $_arr_requests = array();
    
    public function add(SingleRequest $single_request)
    {
        $this->_arr_requests[] = $single_request;
    }
    
    public function execute()
    {
        
    }
}