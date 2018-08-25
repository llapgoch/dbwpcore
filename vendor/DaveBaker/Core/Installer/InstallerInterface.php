<?php

namespace DaveBaker\Core\Installer;
/**
 * Interface InstallerInterface
 * @package DaveBaker\Core\Installer
 */
interface InstallerInterface
    extends ManagerInterface
{
    public function install();
}