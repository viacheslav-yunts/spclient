<?php
/**
 * abstract class BaseSapConfig
 *
 * @package connection
 * @author Hrynchyshyn Uladzimir
 * @version 1.0
 *
 */
namespace Sap\Odatalib\config;


abstract class BaseSapConfig implements BaseConfig
{
    /**
     * @var string
     */
    protected $system;

    /**
     * @var string
     */
    protected $connectionType;

    /**
     * @var string
     */
    protected $configurationFile;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $cookies_path;

    /**
     * @var string
     */
    protected $logs_path;

    /**
     * @var string
     */
    protected $agent;

    /**
     * @var int
     */
    protected $writelog;

    /**
     * @var array
     */
    protected $writelogbylogin;

    /**
     * @var array
     */
    protected $writelogbyservice;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var int
     */
    protected $default;

    /**
     * @return string
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param $system
     * @return $this
     */
    public function setSystem($system)
    {
        $this->system = $system;
        return $this;
    }

    /**
     * @return string
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @param $connectionType
     * @return $this
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;
        return $this;
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
     * @return $this
     */
    public function setConfigurationFile($configurationFile)
    {
        $this->configurationFile = $configurationFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param $server
     * @return $this
     */
    public function setServer($server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookies_path()
    {
        return $this->cookies_path;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setCookies_path($path)
    {
        $this->cookies_path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogs_path()
    {
        return $this->logs_path;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setLogs_path($path)
    {
        $this->logs_path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param $agent
     * @return $this
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @return int
     */
    public function getWritelog()
    {
        return $this->writelog;
    }

    /**
     * @param $writelog
     * @return $this
     */
    public function setWritelog($writelog)
    {
        $this->writelog = $writelog;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getWritelogbylogin()
    {
        return (array) $this->writelogbylogin;
    }
    /**
     * @param $writelogbylogin
     * @return $this
     */
    public function setWritelogbylogin($writelogbylogin)
    {
        $this->writelogbylogin = (array) $writelogbylogin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWritelogbyservice()
    {
        return (array) $this->writelogbyservice;
    }
    /**
     * @param $writelogbyservice
     * @return $this
     */
    public function setWritelogbyservice($writelogbyservice)
    {
        $this->writelogbyservice = (array) $writelogbyservice;
        return $this;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseConnectionType()
    {
        return self::BASE_CONNECTION_TYPE;
    }

    /**
     * @return mixed
     */
    abstract public function getConfig();
}