<?php

namespace Sap\Odatalib;

use Sap\Odatalib\common\ResponseBodyHandler;

/**
 * Class SingleResponse
 * @package Sap\Odatalib
 */
class SingleResponse extends AbstractResponse
{
    /**
     * SingleResponse constructor.
     * @param $headers
     * @param $body
     * @param IRequest $request
     */
    public function __construct($headers, $body, IRequest $request)
    {
        parent::__construct($headers, $body, $request);
        $this->_body = $this->_prepBody($body);
        $this->_initMessages();
    }

    /**
     * @param $body
     * @return mixed
     */
    protected function _prepBody($body)
    {
        return ResponseBodyHandler:: parse($this->getHeader('content-type'), $body);
    }
}