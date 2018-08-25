<?php

namespace DaveBaker\Core\Installer;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
class Manager extends \DaveBaker\Core\Base
{
    /** @var array  */
    protected $installers = [];

    /**
     * @param InstallerInterface $installer
     * @return $this
     */
    public final function add(InstallerInterface $installer)
    {
        $this->installers[] = $installer;
        return $this;
    }

    protected function checkInstall()
    {
        /** @var InstallerInterface $installer */
        foreach($this->installers as $installer){
            $installer->c
        }
    }


}