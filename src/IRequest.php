<?php 
namespace Sap\Odatalib;


/**
* 
*/
interface IRequest
{
    // генерация запроса без вызова
    public function buildQuery();
    // выполнение запроса
    public function execute();
}