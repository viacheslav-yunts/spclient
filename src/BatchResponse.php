<?php
namespace Sap\Odatalib;


class BatchResponse
{
    private $_error = false;
    private $_http_code = 200;
    private $_http_text = '';
    private $_messages  = array();
    private $_headers = array();
    private $_body = '';

    public function __construct($headers, $body)
    {
        $this->_headers = array();

        // Parse the headers list
        $headers = explode("\r\n", $headers);
        foreach ($headers as $header) {
            if (count($arr_header_info = explode(':', $header)) == 2 ) {
                $this->_headers[strtolower($arr_header_info[0])] = ltrim($arr_header_info[1], ' ');
            } else {

                preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
                if ( isset($matches[1])) {
                    $this->_http_code = $matches[1];
                    $this->_http_text = trim(str_replace($matches[0], '', $header));
                }

            }
        }

        $this->_body = $this->_prepBody($body);

        $this->_initMessages();
    }

    private function _prepBody($body)
    {
        $arr_single_responses = array();
        if ($content_type = $this->getHeader('content-type')) {
            preg_match('/boundary=(.*)$/', $content_type, $matches);
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
        }
        return $arr_single_responses;
    }
    
     public function getHttpCode()
    {
        return $this->_http_code;
    }

    public function getHttpCodeText()
    {
        return $this->_http_text;
    }

    public function getHeader($header_name)
    {
        $header_name = strtolower(trim($header_name));
        return isset($this->_headers[$header_name])?$this->_headers[$header_name]:FALSE;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function getData()
    {
        return $this->_body;
    }

    public function hasError()
    {
        return (bool) $this->_error;
    }

    public function getMessages()
    {
        return $this->_messages;
    }
    
    private function _initMessages()
    {
        $this->_messages = [];

        // body messages 
        if (!empty($this->_body->error)) {
            if (isset($this->_body->error->innererror->errordetails)){
                $this->setMessages($this->_body->error->innererror->errordetails);
            } else {
                $this->setMessages(
                    (object) [
                        'message'  => $this->_body->error->message->value,
                        'severity'  => 'warning',
                    ]
                );
            }
        }

        // response messages
        if (! empty($this->_headers['sap-message'])) {
            $messages =  json_decode($this->_headers['sap-message']);
            if (! empty($messages->details)) {
                $this->setMessages($messages->details, true);
            } else {
                $this->setMessages($messages, true);
            }
        }

        // 404 internall error
        if ($this->getHttpCode() >= 400 && $this->getHttpCode() < 500 && $this->getHttpCodeText()) {
            if (empty($this->_messages)) {
                $this->setMessages(
                    (object) [
                        'message'  => $this->getHttpCodeText(),
                        'severity'  => 'warning',
                    ]
                );
            }
        }

        // 500 internall error
        if ($this->getHttpCode() != '200' && isset($this->_body->code[0]) && isset($this->_body->message[0])) {
            $this->setMessages( 
                (object) [
                    'message'  => $this->_body->code[0]->__toString(),
                    'severity'  => 'warning',
                ]
            );
        }

        // для 500 ошибки формируем критическое сообщение
        if ($this->getHttpCode() == '500') {
            $this->setMessages(
                (object) [
                    'message'  => 'В работе сервисов произошла исключительная ситуация, препятствующая возвращению результатов. В случае ее повторения обратитесь к оператору',   
                    'severity'  => 'abort',
                ]
            );
        }
    }

    public function setMessages($params, $url_encode=false )
    {
        if (is_object($params)) {
            $this->_messages[] = array( 
                'message'   =>  ($url_encode)?urldecode( str_replace("'","", $params->message) ):$params->message,
                'type'      =>  $this->_convertType($params->severity),
            ); 
        } elseif (is_array($params)) {
            foreach($params as $m) {
                $this->_messages[] = array( 
                    'message'   =>  ($url_encode)?urldecode( str_replace("'","", $m->message) ):$m->message,
                    'type'      =>  $this->_convertType($m->severity),
                ); 
            }
        }
        
        return true;
    }

    private function _convertType($type)
    {
        switch ($type) {

            case 'abort':
                $type = 'A';
                $this->_error = TRUE;
                break;

            case 'info':
                $type = 'S';
                break;

            case 'warning':
                $type = 'A';
                $this->_error = TRUE;
                break;

            case 'error':
            default:
                $type = 'E';
                $this->_error = TRUE;
                break;

        }

        return $type;
    }
}