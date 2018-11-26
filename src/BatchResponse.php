<?php
namespace Sap\Odatalib;

require_once('common/functions.php');
//use Sap\Odatalib\AbstractResponse;
//use Sap\Odatalib\SingleResponse;

/**
 * Class BatchResponse
 * @package Sap\Odatalib
 */
class BatchResponse extends AbstractResponse
{
    /**
     * BatchResponse constructor.
     * @param $headers
     * @param $body
     */
    public function __construct($headers, $body)
    {
        parent::__construct($headers, $body);
        $this->_body = $this->_prepBody($body);
        $this->_initMessages();
    }

    /**
     * @param $body
     * @return array
     */
    protected function _prepBody($body)
    {
        $arr_single_responses = [];

        if ($content_type = $this->getHeader('content-type')) {

            preg_match('/boundary=(.*)$/', $content_type, $matches);
            if (!empty($matches)) {
                // Fetch each part
                $parts = array_slice(explode($matches[1], $body), 1);

                foreach ($parts as $part) {
                    $part_headers = $part_body = '';
                    // If this is the last part, break
                    if ($part == "--\r\n") {
                        break;
                    }

                    // Separate content from headers
                    $part = ltrim($part, "\r\n");
                    list($part_headers, $raw_body) = explode("\r\n\r\n", substr($part, strpos($part, 'HTTP/1')), 2);

                    $raw_body = explode("\r\n", $raw_body);
                    if (!empty($raw_body)) {
                        foreach ($raw_body as $str) {
                            if (isJson($str)) {
                                $part_body = $str;
                            }
                        }
                    }
                    $arr_single_responses[] = new SingleResponse($part_headers, $part_body);
                }
            } else {
                $arr_single_responses[] = new SingleResponse('', $body);
            }
        }
        return $arr_single_responses;
    }

}