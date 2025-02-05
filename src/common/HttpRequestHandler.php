<?php 
namespace Sap\Odatalib\common;

require_once('functions.php');

//use Sap\Odatalib\common\HttpResponseHandler;
//use Sap\Odatalib\config\BaseConfig;
use Sap\Odatalib\IRequest;

/**
 * Class HttpRequestHandler
 * @package Sap\Odatalib\common
 */
class HttpRequestHandler
{
    use UrlEncodeTrait;

    private $_request;
    private $_request_type;
    private $_config;
    private $_url = '';
    private $_http_request_type = '';
    private $_proxy = false;
    private $_headers = false;
    private $_body = false;

    /**
     * HttpRequestHandler constructor.
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $this->_request = $request;
        $this->_request_type = $request->getRequestType();
        $this->_config = $request->getConfig();
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * @param $request_type
     */
    public function setRequestType($request_type)
    {
        $this->_http_request_type = $request_type;
    }

    /**
     * @param $status
     */
    public function setProxy($status)
    {
        $this->_proxy = (bool) $status;
    }

    /**
     * @param $arr_headers
     */
    public function setHeader($arr_headers)
    {
        $this->_headers = $arr_headers;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * @return array
     */
    public function test()
    {
        return array(
            'url'       => $this->_url,
            'header'    => $this->_headers,
            'body'      => (isJson($this->_body))?json_decode($this->_body,TRUE):$this->_body,
            'config'    => $this->_config
        );
    }

    /**
     * @return \Sap\Odatalib\BatchResponse|\Sap\Odatalib\MultiResponse|\Sap\Odatalib\SingleResponse
     */
    public function execute()
    {

        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $this->url_encode($this->_url)); 
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->_config->getLogin() . ":" . $this->_config->getPassword());
        curl_setopt($curlHandle, CURL_HTTP_VERSION_1_1, true);
        curl_setopt($curlHandle, CURLOPT_COOKIEJAR,  $this->_config->getCookies_path());
        curl_setopt($curlHandle, CURLOPT_COOKIEFILE, $this->_config->getCookies_path());
        curl_setopt($curlHandle, CURLOPT_HEADER, true);
        if (! empty($this->_headers)) {
            $headers = array();
            foreach ($this->_headers as $key => $value) {
                $headers[] = $key.': '.$value;
            }
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'ARMTEK USER AGENT');
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $this->_config->getConnectionTimeout());
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $this->_config->getTimeout());
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING , "gzip");

        switch ($this->_http_request_type) {
            case "GET" :
                curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
                break;
            case "PATCH" :
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PATCH');
            case "POST" :
                curl_setopt($curlHandle, CURLOPT_POST, true);
                curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->_body);
                break;
            default:
                break;
        }

        if (! $httpResponse = curl_exec($curlHandle)) {

            $curlErrorNo = curl_errno($curlHandle);
            switch ($curlErrorNo) {
                case 28 :
                    // curl timeout
                    $curlError = mb_stripos(curl_error($curlHandle), 'connection') !== false
                        ? 'Error! The request was not processed. [The connection is not established]'
                        : 'Error! The request was not processed. [' . $this->_config->getTimeout() . ' sec]';
                    break;
                default :
                    $curlError = curl_error($curlHandle);
                    break;
            }
            $response = HttpResponseHandler::parse($this->_request_type, 'HTTP/1.0 403 ' . (is_string($curlError) ? $curlError : 'Forbidden'), $curlError, $this->_request);

        } else {

            $header_size = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            $response = HttpResponseHandler::parse($this->_request_type, substr($httpResponse, 0, $header_size), substr($httpResponse, $header_size), $this->_request);

        }

        curl_close($curlHandle);

        return $response;
    }
}