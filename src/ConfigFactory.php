<?php
/**
 * class ConfigFactory
 *
 * @package connection
 * @author Hrynchyshyn Uladzimir
 * @version 1.0
 *
 */

namespace connection;


if (!class_exists('\\connection\core\AutoLoader', true)) {
    require_once 'bootstrap.php';
}

/**
 * Класс - фабрика для генерации и получения объекта, содержащего информацию из конфигурационных массивов
 * 
 * Пример вызова:
 *      $connection = new ConfigFactory('development');
 *      $config = $connection->create('sap', 'default');
 *
 * Class ConfigFactory
 * @package connection
 */
class ConfigFactory
{
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
     * Среда разработки (development, production, testing)
     *
     * @var string
     */
    protected $environment;
    
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
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * ConfigFactory constructor.
     *
     * @param $environment
     */
    public function __construct($environment)
    {
        $this->setEnvironment(strtolower($environment));
    }

    /**
     * Создание объекта, содержащего в себе данные запрашиваемого соединения из кофигурационного массива.
     *
     * @param string $system
     * @param string $connectionType
     * @return mixed
     * @throws \Exception
     */
    public function create($system = 'sap', $connectionType = 'default'){

        $this->setSystem(ucfirst($system));
        $this->setConnectionType(ucfirst($connectionType));

        /*
         * Значения переменных ($system и $connectionType) будут актуальны для ситуации,
         * когда класс - обработчик конфига не создан или создан неверно, а секция настроек данного конфига
         * имеется в конфигурационном массиве и при этом валидна. Таким образом на базе дефолтного класса обработчика
         * будет создан конфигурационный объект, заполненный данными из соответствующей секции конфигурационного массива
         */
        $system = strtolower($this->getSystem());
        $connectionType = strtolower($this->getConnectionType());

        /* Проверяем тип подключения (default - да, нет)*/
        if ($this->getConnectionType() != ucfirst(self::DEFAULT_CONNECTION_TYPE)) {
            if (file_exists($this->getFilename())) {
                include_once ($this->getFilename());
                $className = $this->getFullClassName();

                if (class_exists($className)) {
                    $config = new $className($this->getSystem(), strtolower($this->getConnectionType()), $this->getConfigurationFilePath());
                    return $config->getConfig();
                }
            }
        }

        /* В случае не дефолтного типа подключения, или же не прохождения какой-либо вышенаписанной проверки, пытаемся подключиться по default */
        $this->setConnectionType(ucfirst(self::DEFAULT_CONNECTION_TYPE));
        return $this->createDefault($system, $connectionType);
    }

    /**
     * Создание объекта дефолного соединения из кофигурационного массива
     *
     * @return mixed
     * @throws \Exception
     */
    protected function createDefault($system = self::DEFAULT_SYSTEM, $connectionType = self::DEFAULT_CONNECTION_TYPE)
    {
        if (file_exists($this->getFilename())) {
            include_once ($this->getFilename());
            $className = $this->getFullClassName();

            if (class_exists($className)) {
                $config = new $className( $this->getSystem($this->setSystem($system)),
                                          strtolower($this->getConnectionType($this->setConnectionType($connectionType))),
                                          $this->getConfigurationFilePath()
                );
                return $config->getConfig();
            } else {
                throw new \Exception("Не найден класс-обработчик конфигурационных данных");
            }
        } else {
            throw new \Exception("Не найден файл-обработчик конфигурационных данных");
        }
    }

    /**
     * Возвращает имя класса, который будет генерировать конфигурационный объект
     * @return string
     */
    protected function getConfigClassName()
    {
        return $this->getSystem() . $this->getConnectionType() . 'Config';
    }

    /**
     * Возвращает полный путь к файлу, который содержит класс генерирующий конфигурационный объект
     * @return string
     */
    protected function getFilename()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . $this->getSystem() . DIRECTORY_SEPARATOR . $this->getConnectionType() .'Config' . DIRECTORY_SEPARATOR . $this->getConfigClassName() .'.php';
    }

    /**
     * Возвращает полное имя класса (с полным неймспейсом), который будет генерировать конфигурационный объект
     * @return string
     */
    protected function getFullClassName()
    {
        return __NAMESPACE__ . '\\common\\' . $this->getSystem() . '\\' . $this->getConnectionType() . 'Config\\' . $this->getConfigClassName();
    }

    /**
     * Возвращает динамически генерируемый путь к конфигурационному файлу , который содержит массив с конфигурационными данными
     * @return string
     */
    protected function getConfigurationFilePath()
    {
        return APPPATH . 'config' . DIRECTORY_SEPARATOR . $this->getEnvironment() . DIRECTORY_SEPARATOR . strtolower($this->getSystem()) . '_connections.php';
    }
}