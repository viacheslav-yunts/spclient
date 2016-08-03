<?php
namespace Sap\Odatalib\parser;


use Sap\Odatalib\parser\IParser;
abstract class AbstractParser implements IParser
{
     abstract public function parse ($data);
}