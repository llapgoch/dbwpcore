<?php

namespace DaveBaker\Core\Installer;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
class Manager
    extends \DaveBaker\Core\Base
    implements ManagerInterface
{
    /** @var array  */
    protected $installers = [];
    /** @var \DaveBaker\Core\Config\ConfigInterface */
    protected $config;

    /**
     * @param mixed $installers
     * @return $this
     */
    public final function register($installerClass)
    {
        if(!is_array($installerClass)){
            $installerClass = [$installerClass];
        }

        array_map([$this, 'add'], $installerClass);
        return $this;
    }

    /**
     * @param $key stting
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getConfigValue($key)
    {
        return $this->getConfig()->getConfigValue($key);
    }

    /**
     * @return $this
     */
    public function checkInstall()
    {
        /** @var ManagerInterface $installer */
        foreach($this->installers as $installer){
            $installer->checkInstall();
        }

        return $this;
    }

    /**
     * @param $installerClass
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected final function add($installerClass)
    {
        $installerInstance = $this->createAppObject($installerClass);

        if(!($installerInstance instanceof InstallerInterface)){
            throw new Exception("{$installerClass} must be compatible with InstallerInterface");
        }

        $this->installers[] = $installerInstance;
        return $this;
    }

    /**
     * @return \DaveBaker\Core\Config\ConfigInterface|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getConfig()
    {
        if(!$this->config) {
            $this->config = $this->createObject('\DaveBaker\Core\Config\Installer');
        }

        return $this->config;
    }
}