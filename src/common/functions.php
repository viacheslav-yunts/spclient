<?php

if (! function_exists('isJson'))
{
    /**
     * isJson
     *
     *
     * @param    string   $str 
     * @return   bool
     */
    function isJson($str)
    {
        return (is_string($str) && is_array(json_decode($str, true)) && (json_last_error() == JSON_ERROR_NONE));
    }
}