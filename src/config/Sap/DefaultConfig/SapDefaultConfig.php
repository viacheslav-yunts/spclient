<?php
/**
 * class SapDefaultConfig
 *
 * @package connection
 * @author Hrynchyshyn Uladzimir
 * @version 1.0
 *
 */

namespace connection\common\Sap\DefaultConfig;


use connection\common\Sap\SapConfig;

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