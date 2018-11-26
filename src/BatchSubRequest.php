<?php
namespace Sap\Odatalib;

use Sap\Odatalib\config\BaseConfig;

/**
 * Class BatchSubRequest
 * @package Sap\Odatalib
 */
class BatchSubRequest extends SingleRequest implements IRequest
{
    /**
     * @var null
     */
    private $_url = null;

    /**
     * @var null|BaseConfig
     */
    private $_config = null;

    /**
     * @var null
     */
    private $_request_type = null;

    /**
     * BatchSubRequest constructor.
     * @param string $url
     * @param BaseConfig $config
     * @param string $request_type
     */
    public function __construct(string $url, BaseConfig $config, $request_type = 'GET')
    {
        $this->_config = $config;
        $this->setUrl($url);
        $this->setTrasferType($request_type);
    }

    /**
     * @return null
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return null
     */
    public function getRequestType()
    {
        return $this->_request_type;
    }

}