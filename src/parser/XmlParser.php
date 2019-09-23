<?php
namespace Sap\Odatalib\parser;

//use Sap\Odatalib\parser\AbstractParser;

/**
 * Class XmlParser
 * @package Sap\Odatalib\parser
 */
class XmlParser extends AbstractParser
{
    /**
     * @param $data
     * @return \SimpleXMLElement
     */
    public function parse($data)
    {
        return simplexml_load_string(trim($data));
    }
}
