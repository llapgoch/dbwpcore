<?php

namespace DaveBaker\Core\WP\Installer;

interface InstallerInterface
    extends ManagerInterface
{
    public function install();
}