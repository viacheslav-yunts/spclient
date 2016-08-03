<?php
namespace Sap\Odatalib\parser;

use Sap\Odatalib\parser\AbstractParser;
class XmlParser extends AbstractParser
{
    public static function parse($data)
    {
        return simplexml_load_string(trim($data));
    }
}
?>