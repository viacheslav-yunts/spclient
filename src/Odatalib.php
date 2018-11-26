<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
use Sap\Odatalib\ConfigFactory;
use Sap\Odatalib\SingleRequest;
use Sap\Odatalib\MultiRequest;
use Sap\Odatalib\BatchRequest;
use Sap\Odatalib\BatchSubRequest;
use Sap\Odatalib\common\DeliveryBuilder;
use Sap\Odatalib\common\DeliveryDirector;
/**
* 
*/
class Odatalib 
{
    protected $_connection_config = null;

    /**
    * создать объект запроса
    * 
    * @param mixed $method_name
    * @param mixed $method_request_type
    * @param mixed $conn_system
    * @param mixed $conn_type
    * @param mixed $conn_file_path
    * @return SingleRequest
    */
    public function createRequest($method_name, $method_request_type = 'GET', $conn_system = ConfigFactory::DEFAULT_SYSTEM, $conn_type = ConfigFactory::DEFAULT_CONNECTION_TYPE, $conn_file_path = '/')
    {
        $odata_connection_config = $this->createConnectionConfig($conn_system, $conn_type, $conn_file_path);
        //echo "<pre>"; print_r($odata_connection_config); die;
        
        return new SingleRequest($method_name, $odata_connection_config, $method_request_type);
    }

    public function createMultipleRequest()
    {
        return new MultiRequest();
    }

    public function crateBatchGlobRequest($method_name, $method_request_type = 'GET', $conn_system = ConfigFactory::DEFAULT_SYSTEM, $conn_type = ConfigFactory::DEFAULT_CONNECTION_TYPE, $conn_file_path = '/')
    {
        $method_name .= (substr($method_name,-1,1) == '/')?'$batch':'/$batch';
        $odata_connection_config = $this->createConnectionConfig($conn_system, $conn_type, $conn_file_path);
        //echo "<pre>"; print_r($odata_connection_config); die;
        
        return new BatchRequest($method_name, $odata_connection_config, $method_request_type);
    }

    /**
     * @param $methodName
     * @param array $urlParams
     * @param string $methodRequestType
     * @return \Sap\Odatalib\BatchSubRequest
     */
    public function crateBatchSubRequest($methodName, Array $urlParams = [], string $methodRequestType = 'GET')
    {
        return new BatchSubRequest(
            $methodName,
            $this->createConnectionConfig(ConfigFactory::DEFAULT_SYSTEM, ConfigFactory::DEFAULT_CONNECTION_TYPE, '/'),
            $methodRequestType
        );
    }

    /**
    * создание объекта настройки для подключения odata
    * 
    * @param mixed $connection_system
    * @param mixed $connection_type
    * @param mixed $config_file_path
    * @return object 
    */
    public function createConnectionConfig($connection_system = '', $connection_type = '', $config_file_path = '')
    {
        $config_factory = new ConfigFactory();
        $this->_connection_config = $config_factory->create($connection_system, $connection_type, $config_file_path);
        return $this->_connection_config;
    }

    /**
    * сформировать запрос без вызова
    * 
    * @param IRequest $request
    */
    public function buildQuery(IRequest $request)
    {
        $builder = new DeliveryBuilder($request);
        $delivery_director = new DeliveryDirector($builder);
        $delivery_director -> constructRequest();
        $transfer = $builder->getHttp();
        return $transfer->test();
    }
    
    /**
    * софрмировать запрос с вызовом
    * 
    * @param IRequest $request
    */
    public function execute(IRequest $request)
    {
        $builder = new DeliveryBuilder($request);
        $delivery_director = new DeliveryDirector($builder);
        $delivery_director -> constructRequest();
        $transfer = $builder->getHttp();
        return $transfer->execute();
    }
}