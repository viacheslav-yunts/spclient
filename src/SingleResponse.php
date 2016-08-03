<?php
namespace Sap\Odatalib;


use Sap\Odatalib\AbstractResponse;
use Sap\Odatalib\common\ResponseBodyHandler;
class SingleResponse extends AbstractResponse
{
    public function __construct($headers, $body)
    {
        parent::__construct($headers, $body);
        $this->_body = $this->_prepBody($body);
        $this->_initMessages();
    }

    protected function _prepBody($body)
    {
        return ResponseBodyHandler :: parse($this->getHeader('content-type'), $body);
    }
}