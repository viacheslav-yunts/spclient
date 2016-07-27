<?php

use Sap\Odatalib\Odatalib;

class OdatalibTest extends PHPUnit_Framework_TestCase 
{

    public function testCreateRequest()
    {
        $odataLib = new Odatalib;
        
        $method_name = 'test_methos_name';
        $method_type = 'GET';
        $conn_system = 'sap';
        $conn_type   = 'default';
        $conn_file_path = '/home/vyuntsevich/www/app/Resources/connectionsConfig';
        
        $this->assertSame($method_name, $odataLib->createRequest($method_name, $method_type, $conn_system, $conn_type, $conn_file_path)->getUrl());
    }

}