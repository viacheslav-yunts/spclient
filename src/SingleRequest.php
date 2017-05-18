<?php
namespace Sap\Odatalib;


use Sap\Odatalib\common\OdataConstants;
use Sap\Odatalib\IRequest;
use Sap\Odatalib\config\BaseConfig;
class SingleRequest implements IRequest
{
    protected $_type = OdataConstants::SINGLE;

    // вызываемы url    
    private $_url = null;
    // config
    private $_config = null;
    // req_type
    private $_request_type = '';
    // параметры урла
    private $_systemQueryOptions = array(
                '$expand'       => array(),
                '$filter'       => '',
                '$format'       => 'json',
                '$inlinecount'  => array(),
                '$orderby'      => array(),
                '$select'       => array(),
                '$skip'         => '',
                '$top'          => '',
                '$skiptoken'    => ''
    );
    // заголовки запроса
    private $_headers = array();
    // запрос
    private $_body = array();
    
    public function __construct(string $url, BaseConfig $config, $request_type = 'GET')
    {
        $this->_config = $config;
        $this->setUrl($url);
        $this->setTrasferType($request_type);
    }

    public function getConfig()
    {
        return $this->_config; 
    }

    public function constructUrl()
    {
        $url ='http://' . $this->_config->getServer() . '/sap/opu/odata/sap/' . $this->getUrl() . '?';

        if ($this->_request_type == 'GET') {
            foreach ($this->_systemQueryOptions as $key => $val) {
                if (! empty($val)) {
                    if (is_array($val)) {
                        $url .='&'.$key.'='.strtr ( implode(',',$val), array ("&amp;"=> "%26", "&"=> "%26"));
                    } else {
                        $url .='&'.$key.'='.strtr ( $val, array ("&amp;"=> "%26", "&"=> "%26"));
                    }
                }
            }
        }

        return $url;
    }
    
    public function constructBody()
    {
        return json_encode($this->_body);
    }

    /**
    * function setUrl
    *
    * @param string $new_url
    */
    public function setUrl($new_url)
    {
        $this->_url = trim($new_url);
    }
    
    /**
    * function getUrl
    * 
    * @return string $url
    * 
    */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
    * @param mixed $type
    */
    public function setTrasferType($type)
    {
        $this->_request_type = strtoupper(trim($type));
    }
    
    /**
    * @return string $_request_type
    */
    public function getTransferType()
    {
        return $this->_request_type;
    }

    /**
    * @param mixed $header_name
    * @param mixed $header_value
    */
    public function AddHeaderOption($header_name, $header_value)
    {
        $this->_headers[$header_name] = $header_value;
    }
    
    /**
    * @return mixed $_headers
    */
    public function getHeaderOption()
    {
        return $this->_headers;
    }

    /**
    * To add a query option.
    *
    * @param string $option The string value that contains the name of the
    *                 query string option to add
    * @param string $value The string that contains the value of the query
    *                 string option
    */
    public function AddQueryOption($option, $value)
    {
        $option = trim($option);
        if (is_string($value)) $value = trim($value);

        if (array_key_exists($option, $this->_systemQueryOptions)) {

            if (is_array($this->_systemQueryOptions[$option])) $this->_systemQueryOptions[$option][] = $value;
            else $this->_systemQueryOptions[$option] = $value;

        } else {

            $this->_body[$option] = $value;

        }
    }

    // добавляем переменную в запрос
    public function addParam($param_name, $param_value, $wrap_in_quotes =false )
    {
        if ($wrap_in_quotes) $param_value = "'".$param_value."'"; 
        $this->AddQueryOption($param_name, $param_value );
    }

    /**
        * $orderby=<выражение>     
        * Упорядочивает результаты по набору свойств сущности
        * 
        * @param mixed $expression
        */
    public function OrderBy($expression)
    {
        return $this->AddQueryOption('$orderby', $expression);
    }
    /**
    * $top=n     
    * 
    * Ограничивает запрос первыми n сущностями
    * 
    * @param mixed $expression
    */
    public function Top($expression)
    {
        return $this->AddQueryOption('$top', (int) $expression);
    }
        
    /**
    * $skip=n     
    * Пропускает первые n сущностей в наборе
    * 
    * @param mixed $expression
    */
    public function Skip($expression = 0)
    {
        return $this->AddQueryOption('$skip', (int) $expression);
    }

    /**
    * $inlinecount=allpages     
    * Включает счетчик всех сущностей из набора в результат
    */
    public function Inlinecount($expression = 'allpages')
    {
        return $this->AddQueryOption('$inlinecount', $expression);
    } 

    /**
    * $select=<выражение>     
    * Указывает возвращаемое подмножество свойств сущности
    */
    public function Select($expression)
    {
        return $this->AddQueryOption('$select', $expression);
    }

    /**
    * $format     
    * Указывает формат возвращаемого канала (ATOM или JSON). 
    * Этот параметр не поддерживается в WCF Data Services
    */
    public function Format($expression)
    {
        return $this->AddQueryOption('$format', $expression);
    }
    /**
    * $filter=<выражение>     
    * Выражение позволяет ограничивать результаты, возвращаемые запросом 
    * (пример: $filter=Status eq ‘Available’ ограничивает результаты сущностями, 
    * у которых свойство Status имеет значение «Available»).
    * 
    * устанавливает опцию полностью, стирает уже заполлненный до этого фильтр
    */
    public function Filter($expression, $operation_type = 'and')
    {
        $this->_systemQueryOptions['$filter'] .= empty( $this->_systemQueryOptions['$filter'] )?$expression:' '.$operation_type.' '.$expression;
    } 
    public function addFiltr($param_name , $param_value, $is_q =false, $param_type='eq', $arr_type='and') {
        if (is_array($param_value)) {
            $qw = ($is_q) ? "'" : "'";
            $p_value = ( count( $param_value ) == 1 )?'':'(';
            $p_value .= $param_name.' '.$param_type.' '.$qw.implode("$qw $arr_type $param_name $param_type $qw", $param_value).$qw;
            $p_value .= ( count( $param_value ) == 1 )?'':')';
            $this->Filter( $p_value ); 
        } else {
            $p_value = ($is_q) ? "'" . trim($param_value) . "'" : trim($param_value);
            $this->Filter($param_name . ' ' . $param_type . ' ' . $p_value, $arr_type);
        }
    }

    public function getRequestType()
    {
        return $this->_type;
    }
    /**
    * function buildQuery()
    * 
    * @return string $query
    */
    public function buildQuery()
    {
        return true;
    }

    // выполнить запрос
    public function execute()
    {
        try{
            
            // init
            $httpRequest = $this->_initRequest();
            
            // результат работы запроса                                         
            $response = $httpRequest->GetResponse();
            //echo "<pre>"; print_r( $response ); echo "</pre>"; die('end');
            
            return new ODATA_RESPONSE($response);
            
        }catch(InvalidOperation  $e ){
            
            $response = new ODATA_RESPONSE(false);
            $response->setError( true ); 
            
            $messages = (object) array(
                'code'  => 'DUMP',   
                'message'  => $e->getMessage(),   
                'severity'  => 'warning',   
            );
            $response->setMessages( $messages );

        }
        
        return $response;
    }

    private function _initRequest()
    {
        // запрос в сап
        $httpRequest = new HttpRequest(  $this->getTransferType(),
                                             $this->getUrl(),
                                             false,
                                             $this->_headers,
                                             array( 
                                               'system_options' => $this->_systemQueryOptions, 
                                               'custom_params'  => $this->_other, 
                                             ),
                                             false 
                                          );
        return  $httpRequest;   
    }

}