<?php

namespace Sap\Odatalib;

use Sap\Odatalib\config\BaseConfig;
use Sap\Odatalib\config\BaseSapConfig;
use Sap\Odatalib\config\BaseCrmConfig;

/**
 * Класс - фабрика для генерации и получения объекта, содержащего информацию из конфигурационных массивов
 * 
 * Пример вызова:
 *      $connection = new ConfigFactory('development');
 *      $config = $connection->create('sap', 'default');
 *
 * Class ConfigFactory
 *
 * @package connection
 * @author U.Hrynchyshyn
 */
class ConfigFactory
{
    /* Доступные расширения конфигурационных файлов */
    const AVAILABLE_FILE_EXTENSIONS = ['yml', 'php'];

    /* Дефолтное значение системы (sap, crm, etc) */
    const DEFAULT_SYSTEM = 'sap';

    /* Дефолтное значение типа соединения (default, report, etc) */
    const DEFAULT_CONNECTION_TYPE = 'default';

    /**
     * Значение системы (sap, crm, etc)
     *
     * @var string
     */
    protected $system;

    /**
     * Типа соединения (default, report, etc)
     *
     * @var string
     */
    protected $connectionType;

    /**
     * @var string
     */
    protected $configurationFile;

    /**
     * @return string
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param string $system
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * @return string
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @param string $connectionType
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;
    }

    /**
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->configurationFile;
    }

    /**
     * @param $configurationFile
     */
    public function setConfigurationFile($configurationFile)
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * @param string $system
     * @param string $connectionType
     * @param null $pathToConnectionFile
     * @throws \Exception
     */
    public function create($system = 'sap', $connectionType = 'default', $pathToConnectionFile = null)
    {
        $this->setSystem($system);
        $this->setConnectionType($connectionType);
        $this->setConfigurationFile($pathToConnectionFile);

        if (file_exists($this->getConfigurationFile())) {

            $configClassName = $this->getFullClassName();
            if (class_exists($configClassName)) {
                $configObject = new $configClassName($this->getSystem(), $this->getConnectionType(), $this->getConfigurationFile());

                $pathInfo = pathinfo($this->getConfigurationFile());
                $fileExtension = !empty($pathInfo['extension']) ? $pathInfo['extension'] : false;
                if ($fileExtension) {

                    if (in_array($fileExtension, self::AVAILABLE_FILE_EXTENSIONS)) {
                        
                        $connectionsSettings = [];
                        if ($fileExtension == 'php') {
                            include ($this->getConfigurationFile());
                        } elseif ($fileExtension == 'yml') {
                            $connectionsSettings = yaml_parse_file($pathToConnectionFile);
                        }

                        if (is_array($connectionsSettings) && !empty($connectionsSettings)) {
                            return  $this->getConfigFromFile($connectionsSettings, $configObject);
                        } else {
                            throw new \Exception("Ошибка парсинга $fileExtension файла");
                        }
                        
                    } else {
                        throw new \Exception("Рсширение файла $fileExtension не входит в список допустимых расширений конфигурационных файлов");
                    }

                } else {
                    throw new \Exception("Не установленно расширение конфигурационного файла");
                }

            } else {
                throw new \Exception("Класс $configClassName (config object) не найден");
            }

        } else {
            throw new \Exception("Не найден конфигурационный файл");
        }
    }

    /**
     * @return string
     */
    public function getConfigClassName()
    {
        return 'Base' . ucfirst($this->getSystem()) . 'Config';
    }

    /**
     * @return string
     */
    protected function getFullClassName()
    {
        return __NAMESPACE__ . '\\config\\'  . $this->getConfigClassName();
    }

    /**
     * @param array $connectionsSettings
     * @param BaseConfig $configObject
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getConfigFromFile(array $connectionsSettings, BaseConfig $configObject)
    {
        if (!empty($connectionsSettings['parameters']['armtek_connections'][$this->getSystem()][$this->getConnectionType()])) {
            if ($connectionsSettings['parameters']['armtek_connections'][$this->getSystem()][$this->getConnectionType()]['active'] == 1) {
                return $configObject->getConfig($connectionsSettings['parameters']['armtek_connections'][$this->getSystem()][$this->getConnectionType()]);
            }
        }

        $defaultConnection = $this->getDefaultConnectionFromFile($connectionsSettings['parameters']['armtek_connections'][$this->getSystem()]);
        if ($defaultConnection) {
            return $configObject->getConfig($defaultConnection);
        } else {
            throw new \Exception('Не установлено дефолтное соединение для системы ' . $this->getSystem() . ' и типа соединения ' . $this->getConnectionType());
        }
    }

    /**
     * @param array $connectionsSystemSettings
     *
     * @return bool|array
     */
    protected function getDefaultConnectionFromFile(array $connectionsSystemSettings)
    {
        foreach ($connectionsSystemSettings as $connectionType => $connectionSettings) {
            if (($connectionSettings['default'] == 1) && ($connectionSettings['active'] == 1)) {
                return $connectionSettings;
            }
        }

        return false;
    }
}