<?php

namespace Sap\Odatalib;


//use Sap\Odatalib\IRequest;
//use Sap\Odatalib\ConfigFactory;
//use Sap\Odatalib\SingleRequest;
//use Sap\Odatalib\MultiRequest;
//use Sap\Odatalib\BatchRequest;
//use Sap\Odatalib\BatchSubRequest;

use Exception;
use Sap\Odatalib\common\DeliveryBuilder;
use Sap\Odatalib\common\DeliveryDirector;
use Sap\Odatalib\config\BaseSapConfig;

/**
 * Class Odatalib
 * @package Sap\Odatalib
 */
class Odatalib
{
    /**
     * @var null
     */
    protected $_connection_config = null;

    /**
     * создать объект запроса
     *
     * @param $method_name
     * @param string $method_request_type
     * @param string $conn_system
     * @param string $conn_type
     * @param string|array $conn_resource
     * @return SingleRequest
     * @throws Exception
     */
    public function createRequest(
        $method_name,
        $method_request_type = 'GET',
        $conn_system = ConfigFactory::DEFAULT_SYSTEM,
        $conn_type = ConfigFactory::DEFAULT_CONNECTION_TYPE,
        $conn_resource = []
    ) {
        return new SingleRequest(
            $method_name,
            $this->createConnectionConfig($conn_system, $conn_type, $conn_resource),
            $method_request_type
        );
    }

    /**
     * @return MultiRequest
     */
    public function createMultipleRequest()
    {
        return new MultiRequest();
    }

    /**
     * @param $method_name
     * @param string $method_request_type
     * @param string $conn_system
     * @param string $conn_type
     * @param string|array $conn_resource
     * @return BatchRequest
     * @throws Exception
     */
    public function crateBatchGlobRequest(
        $method_name,
        $method_request_type = 'GET',
        $conn_system = ConfigFactory::DEFAULT_SYSTEM,
        $conn_type = ConfigFactory::DEFAULT_CONNECTION_TYPE,
        $conn_resource = []
    ) {
        $method_name .= (substr($method_name, -1, 1) == '/') ? '$batch' : '/$batch';

        return new BatchRequest(
            $method_name,
            $this->createConnectionConfig($conn_system, $conn_type, $conn_resource),
            $method_request_type
        );
    }

    /**
     * @param $methodName
     * @param array $urlParams
     * @param string $methodRequestType
     * @return BatchSubRequest
     */
    public function crateBatchSubRequest($methodName, Array $urlParams = [], string $methodRequestType = 'GET')
    {
        $request = new BatchSubRequest(
            $methodName,
            new BaseSapConfig(ConfigFactory::DEFAULT_SYSTEM, ConfigFactory::DEFAULT_CONNECTION_TYPE, '/'),
            $methodRequestType
        );

        if (!empty($urlParams)) {
            foreach ($urlParams as $param => $value) {
                $request->addParam($param, $value);
            }
        }

        return $request;
    }

    /**
     * создание объекта настройки для подключения odata
     * @param string $connection_system
     * @param string $connection_type
     * @param string|array $conn_resource
     * @return mixed|null
     * @throws Exception
     */
    public function createConnectionConfig($connection_system = '', $connection_type = '', $conn_resource = [])
    {
        $config_factory = new ConfigFactory();
        $this->_connection_config = $config_factory->create($connection_system, $connection_type, $conn_resource);
        return $this->_connection_config;
    }

    /**
     * сформировать запрос без вызова
     * @param IRequest $request
     * @return array
     */
    public function buildQuery(IRequest $request)
    {
        $builder = new DeliveryBuilder($request);
        $delivery_director = new DeliveryDirector($builder);
        $delivery_director->constructRequest();
        $transfer = $builder->getHttp();
        return $transfer->test();
    }

    /**
     * @param IRequest $request
     * @return BatchResponse|MultiResponse|SingleResponse
     */
    public function execute(IRequest $request)
    {
        $builder = new DeliveryBuilder($request);
        $delivery_director = new DeliveryDirector($builder);
        $delivery_director->constructRequest();
        $transfer = $builder->getHttp();
        return $transfer->execute();
    }
}