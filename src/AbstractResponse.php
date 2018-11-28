<?php
namespace Sap\Odatalib;

use Sap\Odatalib\common\OdataConstants;
abstract class AbstractResponse
{
    protected $_error = false;
    protected $_http_code = OdataConstants::HTTP_CODE;
    protected $_http_text = '';
    protected $_messages  = array();
    protected $_headers = array();
    protected $_body = '';
    protected $_request;

    public function __construct($headers, $body, IRequest $request)
    {
        $this->_headers = array();
        $this->_request = $request;

        // Parse the headers list
        $headers = explode("\r\n", $headers);
        foreach ($headers as $header) {
            if (count($arr_header_info = explode(':', $header, 2)) == 2 ) {
                $this->_headers[strtolower($arr_header_info[0])] = ltrim($arr_header_info[1], ' ');
            } else {

                preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
                if ( isset($matches[1])) {
                    $this->_http_code = $matches[1];
                    $this->_http_text = trim(str_replace($matches[0], '', $header));
                }

            }
        }

        $this->_body = $body;

    }

    abstract protected function _prepBody($body);

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
    
    public function hasError()
    {
        return (bool) $this->_error;
    }

    public function getMessages()
    {
        return $this->_messages;
    }

    public function getData()
    {
        return $this->_body;
    }

    protected function _initMessages()
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
                        'severity'  => 'abort',
                    ]
                );
            }
        }

        // response messages
        if (! empty($this->_headers['sap-message'])) {
            $messages =  json_decode($this->_headers['sap-message']);
            if (! empty($messages->message)) $this->setMessages($messages, true);
            if (! empty($messages->details)) $this->setMessages($messages->details, true);
        }

        // 404 internall error
        if ($this->getHttpCode() >= 400 && $this->getHttpCode() < 500 && $this->getHttpCodeText()) {
            if (empty($this->_messages)) {
                $this->setMessages(
                    (object) [
                        'message'  => $this->getHttpCodeText(),
                        'severity'  => 'abort',
                    ]
                );
            }
        }

        // 500 internall error
        if ($this->getHttpCode() != '200' && isset($this->_body->code[0]) && isset($this->_body->message[0])) {
            $this->setMessages( 
                (object) [
                    'message'  => $this->_body->code[0]->__toString(),
                    'severity'  => 'abort',
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

    protected function _convertType($type)
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
                $type = 'W';
                //$type = 'A';
                //$this->_error = TRUE;
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