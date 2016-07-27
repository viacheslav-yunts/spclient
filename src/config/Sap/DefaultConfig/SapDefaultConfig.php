<?php
/**
 * class SapDefaultConfig
 *
 * @package connection
 * @author Hrynchyshyn Uladzimir
 * @version 1.0
 *
 */

namespace Sap\Odatalib\config\Sap\DefaultConfig;


use Sap\Odatalib\config\Sap\SapConfig;

class SapDefaultConfig extends SapConfig
{
    public function __construct($system, $connectionType, $configurationFile)
    {
        parent::__construct($system, $connectionType, $configurationFile);
    }

    public function getConfig()
    {
        return parent::getConfig();
    }
}