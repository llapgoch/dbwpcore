<?php

namespace DaveBaker\Core\Installer;
/**
 * Interface InstallerInterface
 * @package DaveBaker\Core\Installer
 */
interface ManagerInterface
{
    public function checkInstall();
    public function register($installers);
    public function getConfigValue($key);
}