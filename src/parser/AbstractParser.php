<?php

namespace Sap\Odatalib\parser;


//use Sap\Odatalib\parser\IParser;

/**
 * Class AbstractParser
 * @package Sap\Odatalib\parser
 */
abstract class AbstractParser implements IParser
{
    abstract public function parse($data);
}