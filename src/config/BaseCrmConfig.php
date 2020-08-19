<?php

namespace Sap\Odatalib\config;

class BaseCrmConfig extends BaseConfig
{
    /**
     * SapConfig constructor.
     *
     * @param string $system
     * @param string $connectionType
     * @param string $configurationFile
     */
    public function __construct(string $system, string $connectionType, string $configurationFile)
    {
        $this->setSystem($system);
        $this->setConnectionType($connectionType);
        $this->setConfigurationFile($configurationFile);
        $this->setServicePrefix('/sap/opu/odata/sap/');
    }

    /**
     * @param array $parameters
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function getConfig(array $parameters)
    {
        if ($this->isValid($parameters)) {
            return $this;
        }

        throw new \Exception("Непройдена валидация конфигурационных параметров");
    }
}