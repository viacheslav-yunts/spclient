<?php
namespace Sap\Odatalib\parser;

class JsonParser
{
    public static function parse($data)
    {
        $resp = json_decode($data);
        if (isset($resp->d)) $resp = $resp->d;
        return isset($resp->results)?$resp->results:$resp;
    }
}
?>