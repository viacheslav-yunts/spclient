<?php 
namespace Sap\Odatalib\common;


use Sap\Odatalib\common\OdataConstants;
use Sap\Odatalib\parser\JsonParser;
use Sap\Odatalib\parser\TxtParser;
class ResponseBodyHandler
{
    public static function parse($request_type, $content)
    {

        switch (self :: checkRequestType($request_type)) {

            case OdataConstants::APPLICATION_JSON :
                $content = JsonParser::parse($content);
                break;

            case OdataConstants::TEXT_PLAIN :
            default :
                $content = TxtParser::parse($content);
                break;
        }

        return $content;
    }

    public static function checkRequestType($request_type)
    {
        list($type, $charset) = explode(';', $request_type, 2);
        if (empty($type)) $type = OdataConstants::CONTENT_TYPE_DEFAULT;
        return $type;
    }
}
