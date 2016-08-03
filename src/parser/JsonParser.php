<?php
namespace Sap\Odatalib\parser;

use Sap\Odatalib\parser\AbstractParser;
class JsonParser extends AbstractParser
{
    public static function parse($data)
    {
        $resp = json_decode($data);
        return isset($resp->d)?$resp->d:$resp;
    }
}
?>