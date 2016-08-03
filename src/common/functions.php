<?php

if (! function_exists('isJson'))
{
    /**
     * isJson
     *
     *
     * @param    string   строка 
     * @return   bool
     */
    function isJson($string)
    {
        return (is_string($str) && is_array(json_decode($str, true)) && (json_last_error() == JSON_ERROR_NONE));
    }
}