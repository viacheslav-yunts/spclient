<?php
/**
 * interface BaseConfig
 *
 * @package connection
 * @author Hrynchyshyn Uladzimir
 * @version 1.0
 *
 */

namespace Sap\Odatalib\config;


interface BaseConfig
{
    /* Дефолтное название типа соединения с сервером */
    const BASE_CONNECTION_TYPE = 'default';

    /* Возвращает объект, заполненный данными из массива конфигурационного файла */
    function getConfig();

    /* Возвращает название дефолтного соединения с сервером */
    function getBaseConnectionType();
}