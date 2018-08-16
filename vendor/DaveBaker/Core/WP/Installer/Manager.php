<?php

namespace DaveBaker\Core\WP\Installer;

class Manager extends \DaveBaker\Core\WP\Base
{
    const VERSION_OPTION = 'version';

    protected $namespaceSuffix = "installer_";

    /** Override to run local installers */
    protected function install(){
    }
    
    public function checkInstall()
    {
        /** @var $config \DaveBaker\Core\WP\Config\Installer */
        $config = $this->getApp()->getObjectManager()->get('\DaveBaker\Core\WP\Config\Installer');
        $installedVersion = $this->getOptionManager()->get(self::VERSION_OPTION);
        $currentVersion = $config->getConfigValue(self::VERSION_OPTION);

        if(version_compare($currentVersion, $installedVersion, ">")){
            try {
                $this->install();

                // Upgrade the version in the database
                $this->getOptionManager()->set(self::VERSION_OPTION, $currentVersion);
            }catch (\Exception $e){
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }
}