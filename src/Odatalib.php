<?php
namespace Sap\Odatalib;


use Sap\Odatalib\ConfigFactory;
/**
* 
*/
class Odatalib 
{
    /**
    * создание объекта настройки для подключения odata
    * 
    * @param mixed $connection_system
    * @param mixed $connection_type
    * @param mixed $config_file_path
    * @return object 
    */
    public function createConnectionConfig($connection_system = '', $connection_type = '', $config_file_path='')
    {
        $config_factory = new ConfigFactory();
        return $config_factory->create($connection_system, $connection_type, $config_file_path);
    }

}