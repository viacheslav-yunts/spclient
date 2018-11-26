<?php
namespace Sap\Odatalib;

/**
 * Class BatchSubRequest
 * @package Sap\Odatalib
 */
class BatchSubRequest extends SingleRequest implements IRequest
{

    /**
     * @return null
     */
    public function constructUrl()
    {
        return $this->getUrl() . '?' . $this->getSystemQueryOptionsToString();
    }

    /**
     * @return null
     */
    public function getRequestType()
    {
        return $this->_request_type;
    }

}