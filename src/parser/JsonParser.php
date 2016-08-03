<?php
namespace Sap\Odatalib\parser;

class JsonParser
{
    public static function parse($data)
    {
        $resp = json_decode($data);
        return isset($resp->d)?$resp->d:$resp;
    }
}
?>