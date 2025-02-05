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
    const AVAILABLE_FILE_EXTENSIONS = ['yml', 'php', 'yaml'];

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
     * @var string|array
     */
    protected $configurationResource = [];

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
     * @return string|array
     */
    public function getConfigurationResource()
    {
        return $this->configurationResource;
    }

    /**
     * @param string|array $configurationFile
     */
    public function setConfigurationResource($resource)
    {
        $connectionsSettings = [];

        if (is_array($resource)) {
            $connectionsSettings = $resource;
        } else {

            if (file_exists($resource)) {

                $pathInfo = pathinfo($resource);
                $fileExtension = !empty($pathInfo['extension']) ? $pathInfo['extension'] : false;
                if ($fileExtension) {

                    if (in_array($fileExtension, self::AVAILABLE_FILE_EXTENSIONS)) {

                        if ($fileExtension == 'php') {
                            include($resource);
                        } elseif ($fileExtension == 'yml' || $fileExtension == 'yaml') {
                            $connectionsSettings = yaml_parse_file($resource);
                        }

                    } else {
                        throw new \Exception("File extension $fileExtension it is not included in the list of acceptable configuration file extensions.");
                    }

                } else {
                    throw new \Exception("The configuration file extension is not installed.");
                }

            } else {
                throw new \Exception("The configuration file was not found.");
            }

        }

        $this->configurationResource = $connectionsSettings;
    }

    /**
     * @param string $system
     * @param string $connectionType
     * @param string|array $connectionsSettingsResource
     * @throws \Exception
     */
    public function create($system = 'sap', $connectionType = 'default', $connectionsSettingsResource = [])
    {
        $this->setSystem($system);
        $this->setConnectionType($connectionType);
        $this->setConfigurationResource($connectionsSettingsResource);

        $configClassName = $this->getFullClassName();

        if (class_exists($configClassName)) {

            $configObject = new $configClassName(
                $this->getSystem(),
                $this->getConnectionType()
            );

            return $this->getConfigFromFile($this->getConfigurationResource(), $configObject);

        } else {
            throw new \Exception("Class $configClassName (config object) not found");
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
        return __NAMESPACE__ . '\\config\\' . $this->getConfigClassName();
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
            throw new \Exception('The default connection for the ' . $this->getSystem() . ' system has not been established and the type of ' . $this->getConnectionType() . ' connection');
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