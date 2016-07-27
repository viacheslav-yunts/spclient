<?php
namespace Sap\Odatalib;


use Sap\Odatalib\ConfigFactory;
use Sap\Odatalib\SingleRequest;
/**
* 
*/
class Odatalib 
{
    
    public function createRequest($method_name, $method_request_type = 'GET', $conn_system = ConfigFactory::DEFAULT_SYSTEM, $conn_type = ConfigFactory::DEFAULT_CONNECTION_TYPE, $conn_file_path = '/')
    {
        $odata_connection_config = $this->createConnectionConfig($conn_system, $conn_type, $conn_file_path);
        //echo "<pre>"; print_r($odata_connection_config); die;
        
        return new SingleRequest($method_name, $odata_connection_config, $method_request_type);
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
        return $config_factory->create($connection_system, $connection_type, $config_file_path);
    }

}