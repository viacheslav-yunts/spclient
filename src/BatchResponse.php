<?php
namespace Sap\Odatalib;

require_once('common/functions.php');

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
    public function __construct($headers, $body, IRequest $request)
    {
        parent::__construct($headers, $body, $request);
        $this->_body = $this->_prepBody($body);
        $this->_initMessages();
    }


    /**
     * @param $key
     * @return bool
     */
    public function hasSubResponse($key)
    {
        return isset($this->_body[$key]);
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function getSubResponse($key)
    {
        if (isset($this->_body[$key])) {
            return $this->_body[$key];
        } else {
            throw new \Exception("Invalid key $key.");
        }
    }

    /**
     * @param $body
     * @return array
     */
    protected function _prepBody($body)
    {
        $subRequestsKeys = array_keys($this->_request->_requests);
        $arr_single_responses = [];

        if ($content_type = $this->getHeader('content-type')) {

            preg_match('/boundary=(.*)$/', $content_type, $matches);
            if (!empty($matches)) {
                // Fetch each part
                $parts = array_slice(explode($matches[1], $body), 1);

                foreach ($parts as $part) {
                    $part_body = '';
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

                    $key = array_shift($subRequestsKeys);
                    $arr_single_responses[$key] = new SingleResponse($part_headers, $part_body, $this->_request->get($key));
                }
            } else {
                $key = array_shift($subRequestsKeys);
                $arr_single_responses[] = new SingleResponse('', $body, $this->_request->get($key));
            }
        }
        return $arr_single_responses;
    }

    /**
     *
     * Инициализация сообщений
     *
     */
    protected function _initMessages()
    {

        parent::_initMessages();

        if (is_array($this->_body)) {
            foreach ($this->_body as $key => $response) {
                if ($response instanceof AbstractResponse) {
                    $this->_messages = array_merge($this->_messages, $response->getMessages());
                }
            }
        }

    }

}