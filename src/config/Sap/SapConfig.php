<?php
/**
 * Created by Hrynchyshyn.
 */

namespace Sap\Odatalib\config\Sap;


use Sap\Odatalib\config\BaseSapConfig;

class SapConfig extends BaseSapConfig
{
    /**
     * В конструкторе класса мы инициализируем такие параметры как:
     * $system - (sap, crm, etc.)
     * $connectionType - (default, report, etc)
     * $configurationFile - (путь к файлу - содержащему конфигурационный массив)
     *
     * SapConfig constructor.
     * @param $system
     * @param $connectionType
     * @param $configurationFile
     */
    public function __construct($system, $connectionType, $configurationFile)
    {
        $this->setSystem($system);
        $this->setConnectionType($connectionType);
        $this->setConfigurationFile($configurationFile);
    }

    /**
     * Метод возвращает объект - содержащий в себе информацию из конфигурационного массива
     *
     * @return $this|SapConfig
     * @throws \Exception
     */
    public function getConfig()
    {
        $config = [];
        /* Проверяем существование конфигурационного файла */

        if (file_exists($this->getConfigurationFile())) {
            /* Если конфигурационный файл существует , то подключаем его */
            require $this->getConfigurationFile();
            //$config = yaml_parse(file_get_contents($this->getConfigurationFile()));
            
            /* Тип соединения default ? да - нет */
            if ($this->getConnectionType() != $this->getBaseConnectionType()) {
                /* Проверяем есть ли в конфигурационном массиве секция с нужным нам типом подключения */
                if(isset($config['connections'][$this->getConnectionType()]) && !empty($config['connections'][$this->getConnectionType()])) {
                    /* Проверяем на валидность секцию в конфигурационном массиве */
                    if ($this->isValid($config['connections'][$this->getConnectionType()])) {
                        /* Проверка секции на on - off*/
                        if ($this->getActive() == 1) {
                            /* Возвращаем объект, свойства которого забиты данными из секции в конфигурационном файле */
                            return $this;
                        }
                    }
                }

                /*
                * Если тип нужного нам подключения не прошел вышестоящие проверки,
                * то мы пытаемся подключиться по данным из default секции конфигурационного массива.
                */
                $this->setConnectionType($this->getBaseConnectionType());
                return $this->getConfig();

            } else {
                /*
                 * Данный блок предназначен для попытки подключения по данным из default секции конфигурационного массива
                 * в случае не прохождения одной из нижестоящих проверок, будет создан соответствующий exception
                 */
                if(isset($config['connections'][$this->getBaseConnectionType()]) && !empty($config['connections'][$this->getBaseConnectionType()])) {
                    if ($this->isValid($config['connections'][$this->getBaseConnectionType()])) {
                        if ($this->getActive() == 1) {
                            return $this;
                        } else {
                            throw new \Exception("Подключение не активно");
                        }
                    } else {
                        throw new \Exception("Непройдена валидация конфигурационных параметров");
                    }
                } else {
                    throw new \Exception("Не найден конфигурационный массив");
                }
            }
        } else {
            throw new \Exception("Не найден конфигурационный файл");
        }
    }

    /**
     * Метод для валидации параметров секции из конфигурационного массива.
     * Выполняется проверка на существование и пустоту.
     * При прохождении проверки, все данные из секции через сеттеры помещаются
     * в объект, который впоследствии будет возвращен методом getConfig();
     *
     * @param array $config
     * @return bool
     */
    protected function isValid(array $config)
    {
        foreach ($config as $key => $value) {
            if (isset($key) && (!empty($value) || $value === 0)) {
                $property = 'set'.ucfirst($key);
                $this->$property($value);
            } else {
                return false;
            }
        }
        return true;
    }
}