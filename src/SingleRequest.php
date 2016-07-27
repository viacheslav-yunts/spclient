<?php
namespace Sap\Odatalib;


use Sap\Odatalib\IRequest;
class SingleRequest implements IRequest
{
    // вызываемы url    
    private $_url = null;
    // config
    private $_config = null;
    // заголовки запроса
    private $_headers = false;
    // запрос
    private $_body = false;
    
    public function __construct(string $url, BaseSapConfig $config, $request_type = 'GET'){
        $this->_url = $url;
        $this->_config = $config;
    }

    /**
    * function setUrl
    *                                                         
    * @param string $new_url
    */
    public function setUrl( $new_url )
    {
        $this->_url = $new_url;
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
    * установить таймаут
    */
    public function setTimeout( $new_timeout ){
        $this->_timeout = $new_timeout;
    }
    
    /**
    * получить текущий установленный таймаут
    */
    public function getTimeout(){
        return $this->_timeout;
    }
    /**
    * установить пользователя сап
    */
    public function setOdataUserName($user_name){
        $this->_user_name = $user_name;
    }

    /**
    * получить пользователя сап
    */
    public function getOdataUserName(){
        return $this->_user_name;
    }
    /**
    * установить пароль пользователя сап
    */
    public function setOdataUserPassword($user_passwd){
        $this->_user_password = $user_passwd;
    }

    /**
    * получить пароль пользователя сап
    */
    public function getOdataUserPassword(){
        return $this->_user_password;
    }
        
    /**
    * function setTrasferType
    * 
    * @param mixed $type
    */
    public function setTrasferType( $type )
    {
        $type = strtoupper( $type );
        if( in_array( $type, $this->_arrTransferTypes ) )
        {
            $this->_req_type =  $type;
            return true;
        }
        return false;
    }
    
    /**
    * function getTransferType
    * 
    * @return string $_req_type
    */
    public function getTransferType()
    {
        return $this->_req_type;
    }
     
    /**
    * function addHeaderOption
    * 
    * @param mixed $header_name
    * @param mixed $header_value
    */
    public function AddHeaderOption($header_name, $header_value)
    {
        $this->_headers[ $header_name ] =  $header_value;
    }
    
    /**
    * function  getHeaderOption
    *     
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
        if( is_string($value) )$value  = trim($value);

        
        if( array_key_exists( $option,  $this->systemQueryOptions ) )
        {
            if( is_array( $this->systemQueryOptions[$option] ) ) $this->systemQueryOptions[$option][] = $value;
            else $this->systemQueryOptions[$option] = $value;   
            
        }else{
            
            $this->_other[ $option ] =  $value;    
        }
        
        return $this;
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
        * $filter=<выражение>     
        * Выражение позволяет ограничивать результаты, возвращаемые запросом 
        * (пример: $filter=Status eq ‘Available’ ограничивает результаты сущностями, 
        * у которых свойство Status имеет значение «Available»).
        * 
        * устанавливает опцию полностью, стирает уже заполлненный до этого фильтр
        */
    public function Filter($expression, $operation_type = 'and')
    {
        $this->systemQueryOptions['$filter'] .= empty( $this->systemQueryOptions['$filter'] )?$expression:' '.$operation_type.' '.$expression;
    } 
    public function addFiltr( $param_name , $param_value, $is_q =false, $param_type='eq', $arr_type='and' ){
        if( is_array( $param_value ) ){
            $qw = ($is_q)?"'":"'";
            $p_value = ( count( $param_value ) == 1 )?'':'(';
            $p_value .= $param_name.' '.$param_type.' '.$qw.implode("$qw $arr_type $param_name $param_type $qw", $param_value).$qw;
            $p_value .= ( count( $param_value ) == 1 )?'':')';
            $this->Filter( $p_value ); 
        }else{
            if( $is_q ) $p_value = "'".trim($param_value)."'";
            //@anton : добавил arr_type в вызов Filter, чтобы коректно обрабатывалась фильтрация через or
            $this->Filter( $param_name.' '.$param_type.' '.$p_value, $arr_type ); 
        }
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
    public function Inlinecount($expression='allpages')
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
    * function buildQuery()
    * 
    * @return string $query
    */
    public function buildQuery(){
        
        $httpRequest = $this->_initRequest();
        
        $query = $httpRequest->buildQuery();    
        
        //echo "<pre>"; print_r( $query ); echo "</pre>"; die;
        
        return $query;
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
            
            return new ODATA_RESPONSE( $response );
            
        }catch(InvalidOperation  $e ){
            
            $response = new ODATA_RESPONSE( false );
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
                                               'system_options' => $this->systemQueryOptions, 
                                               'custom_params'  => $this->_other, 
                                             ),
                                             false 
                                          );
        return  $httpRequest;   
    }
    
    
}