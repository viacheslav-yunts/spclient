<?php 
namespace Sap\Odatalib\common;


namespace Sap\Odatalib\Response;
class HttpResponseParser
{
    public static function parse($header, $body, $content_type)
    {
        echo "<pre>"; print_r($header);
        echo "<pre>"; print_r($body);
        return new Response($header, $body);
    }
}

/*
            // grab multipart boundary from content type header
            preg_match('/boundary=(.*)$/', curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE), $matches);
            $boundary = $matches[1];
            echo "<pre>"; print_r($boundary);

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