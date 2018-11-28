<?php 
namespace Sap\Odatalib;


/**
* 
*/
interface IRequest
{
    // тип запроса
    public function getRequestType();

    // генерация запроса без вызова
    public function buildQuery();

    // выполнение запроса
    public function execute();

    // получение конфигов
    public function getConfig();

}