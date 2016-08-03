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
        list($request_type) = explode(';', $request_type, 1);
        if (empty($request_type)) $request_type = OdataConstants::CONTENT_TYPE_DEFAULT;
        echo $request_type;
        return $request_type;
    }
}
/*
            // grab multipart boundary from content type header
        //preg_match('/boundary=(.*)$/', $content_type, $matches);
        //$boundary = $matches[1];
        //echo "<pre>"; print_r($boundary);

        
            // Fetch each part
            $parts = array_slice(explode($boundary, $body), 1);
            $data = array();

            foreach ($parts as $part) {
                // If this is the last part, break
                if ($part == "--\r\n") break; 

                // Separate content from headers
                $part = ltrim($part, "\r\n");
                list($raw_headers, $raw_body) = explode("\r\n\r\n", substr($part, strpos($part,'HTTP/1')), 2);

                // Parse the headers list
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    if (count($arr_header_info = explode(':', $header)) == 2 ) {
                        $headers[strtolower($arr_header_info[0])] = ltrim($arr_header_info[1], ' ');
                    }
                }
                echo "<pre>"; print_r($headers);
                
                $raw_body = explode("\r\n", $raw_body);
                foreach ($raw_body as $str) {
                    if (is_string($str) && is_array(json_decode($str, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $body = $str;
                    }
                }
                echo "<pre>"; print_r($body);
                
                
            }
            */