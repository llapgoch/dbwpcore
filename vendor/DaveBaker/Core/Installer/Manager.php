<?php

namespace DaveBaker\Core\Installer;

abstract class Manager
    extends \DaveBaker\Core\Base
    implements ManagerInterface
{
    const VERSION_OPTION = 'version';

    /** @var string  */
    protected $namespaceCode = "installer";

    /** Override to run local installers */
    public abstract function install();

    public function checkInstall()
    {
        /** @var $config \DaveBaker\Core\Config\Installer */
        $config = $this->getApp()->getObjectManager()->get('\DaveBaker\Core\Config\Installer');
        $installedVersion = $this->getOption(self::VERSION_OPTION);
        $currentVersion = $config->getConfigValue(self::VERSION_OPTION);

        if(version_compare($currentVersion, $installedVersion, ">")){
            try {
                $this->install();

                // Upgrade the version in the database
                $this->setOption(self::VERSION_OPTION, $currentVersion);
            }catch (\Exception $e){
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }
}