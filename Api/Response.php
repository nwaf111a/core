<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Api;

use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Arikaim;
use Closure;

/**
 * Api Respnse support JSON format only.
*/
class Response 
{
    /**
     * response result
     *
     * @var array
     */
    protected $result;

    /**
     * Errors list
     *
     * @var array
     */
    protected $errors; 

    /**
     * Denug mode
     *
     * @var bool
     */
    protected $debug;

    /**
     * pretty format json 
     *
     * @var bool
     */
    protected $pretty_format;

    /**
     * Raw json response
     *
     * @var boolean
     */
    protected $raw;

    /**
     * Request response object
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param boolean $debug
     */
    public function __construct($debug = false, $response = null) 
    {                    
        $this->errors = [];
        $this->debug = ($debug == true) ? true : false;
        $this->result['result'] = null;
        $this->result['status'] = "ok";  
        $this->result['code'] = 200; 
        $this->result['errors'] = $this->errors;  
        $this->pretty_format = false; 
        $this->raw = false; 
        $this->response = ($response == null) ? Arikaim::getApp()->getResponseFactory()->createResponse() : $response;
    }

    /**
     * Set json pretty format to true
     *
     * @return Response
     */
    public function useJsonPrettyformat()
    {
        $this->pretty_format = true;
        return $this;
    }

    /**
     * Add errors
     *
     * @param array $errors
     * @return void
     */
    public function addErrors(array $errors)
    {
        if (is_array($errors) == false) {
            return false;
        }
        $this->errors = array_merge($this->errors,$errors);       
    }

    /**
     * Set errors 
     *
     * @param array $errors
     * @return void
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Clear all errors.
     *
     * @return void
    */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Set error message
     *
     * @param string $error_message
     * @param boolean $condition
     * @return void
     */
    public function setError($error_message, $condition = true) 
    {
        if ($condition !== false) {
            array_push($this->errors,$error_message);  
        }               
    }

    /**
     * Set error message
     *
     * @param string $error_message
     * @param boolean $condition
     * @return Response
     */
    public function withError($error_message, $condition = true) 
    {
        $this->setError($error_message,$condition);
        return $this;
    }

    /**
     * Set response result
     *
     * @param mixed $data
   
     * @return Response
     */
    public function setResult($data) 
    {
        $this->result['result'] = $data;      
        return $this;
    }

    /**
     * Set response 
     *
     * @param boolean $condition
     * @param array|Closure $data
     * @param string|Closure $error
     * @return mixed
     */
    public function setResponse($condition, $data, $error)
    {
        if ($condition !== false) {
            if (is_callable($data) == true) {
                return $data();
            } 
            if (is_array($data) == true) {
                return $this->setResult($data);
            }
            if (is_string($data) == true) {
                return $this->message($data);
            }

        } else {
            return (is_callable($error) == true) ? $error() : $this->setError($error);          
        }
    }

    /**
     * Set result message
     *
     * @param string $message
     * @return Response
     */
    public function message($message)
    {
        return $this->field('message',$message);       
    }

    /**
     * Set field to result array 
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setResultField($name, $value)
    {      
        $this->result['result'][$name] = $value;
    }

    /**
     * Set result field 
     *
     * @param string $name
     * @param mixed $value
     * @return Response
     */
    public function field($name, $value)
    {
        $this->setResultField($name,$value);
        return $this;
    }

    /**
     * Return errors count
     *
     * @return int
     */
    public function getErrorCount()
    {
        return count($this->errors);
    }

    /**
     * Return true if response have error
     *
     * @return boolean
     */
    public function hasError() 
    {    
        return ($this->getErrorCount() > 0) ? true : false;          
    }

    /**
     * Return request response
     *     
     * @param boolean $raw
     *  
     * @return Slim\Http\Response
     */
    public function getResponse($raw = false) 
    {           
        $this->raw = $raw;

        $json = $this->getResponseJson();
        $this->response->getBody()->write($json);

        return $this->response->withStatus($this->result['code'])->withHeader('Content-Type','application/json');           
    }

    /**
     * Return json 
     *
     * @return string
     */
    public function getResponseJson()
    {
        $this->result['errors'] = $this->errors;
        if ($this->hasError() == true) {
            $this->result['status'] = "error"; 
            $this->result['code'] = 400;
        }
        
        if ($this->debug == true) {
            $this->result['execution_time'] = Arikaim::getExecutionTime();
            $this->result['memory_usage'] = Utils::getMemorySizeText(memory_get_usage(true));
        }
        $result = ($this->raw == true) ? $this->result['result'] : $this->result;
    
        return ($this->pretty_format == true) ? Utils::jsonEncode($result) : json_encode($result,true);      
    }    
}
