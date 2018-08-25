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
     * @param $installerClass
     * @return $this
     * @throws Exception
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

    public function checkInstall()
    {
        /** @var ManagerInterface $installer */
        foreach($this->installers as $installer){
            $installer->checkInstall();
        }
    }
}