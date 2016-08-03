<?php
namespace Sap\Odatalib\parser;


use Sap\Odatalib\parser\IParser;
abstract class AbstractParser implements IParser
{
     public static function parse ($data);
}