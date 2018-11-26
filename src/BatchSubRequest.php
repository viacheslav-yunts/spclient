<?php
namespace Sap\Odatalib;

use Sap\Odatalib\common\UrlEncodeTrait;

/**
 * Class BatchSubRequest
 * @package Sap\Odatalib
 */
class BatchSubRequest extends SingleRequest implements IRequest
{
    use UrlEncodeTrait;

    /**
     * @return null
     */
    public function constructUrl()
    {
        return $this->getUrl() . '?' . $this->url_encode($this->getSystemQueryOptionsToString());
    }

    /**
     * @return null
     */
    public function getRequestType()
    {
        return $this->_request_type;
    }

}