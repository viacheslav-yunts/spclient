<?php

use Sap\Odatalib\Odatalib;

class OdatalibTest extends PHPUnit_Framework_TestCase 
{

    public function testNachHasCheese()
    {
        $odataLib = new Odatalib;
        $this->assertTrue($odataLib->hasCheese());
    }

}