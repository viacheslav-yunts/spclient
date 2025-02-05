<?php

namespace Sap\Odatalib\config;

class BaseEwmConfig extends BaseConfig
{
    /**
     * SapConfig constructor.
     *
     * @param string $system
     * @param string $connectionType
     */
    public function __construct(string $system, string $connectionType)
    {
        $this->setSystem($system);
        $this->setConnectionType($connectionType);
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

        throw new \Exception("Validation of configuration parameters failed");
    }
}