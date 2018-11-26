<?php

namespace Sap\Odatalib;


//use Sap\Odatalib\IRequest;
//use Sap\Odatalib\SingleRequest;
class BatchSubRequest
{
    /**
     * @var null
     */
    private $_url = null;

    /**
     * @var null
     */
    private $_req_type = null;

    /**
     * @var array
     */
    private $params = [];

    /**
     * BatchSubRequest constructor.
     * @param $methodName
     * @param array $urlParams
     * @param string $methodRequestType
     */
    public function __construct($methodName, Array $urlParams = [], string $methodRequestType = 'GET')
    {
        $this->_url = $methodName;
        $this->_req_type = $methodRequestType;
        if (!empty($urlParams)) {
            foreach ($urlParams as $key => $value) {
                $this->addParam($key, $value);
            }
        }
    }

    /**
     * @return null
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return null
     */
    public function getRequestType()
    {
        return $this->_req_type;
    }

    /**
     * добавляем переменную в запрос
     * @param $param_name
     * @param $param_value
     * @param bool $wrap_in_quotes
     */
    public function addParam($param_name, $param_value, $wrap_in_quotes = false)
    {
        if ($wrap_in_quotes) {
            $param_value = "'" . $param_value . "'";
        }
        $this->params[$param_name] = $param_value;
        //$this->AddQueryOption($param_name, $param_value );
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}