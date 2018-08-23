<?php

namespace DaveBaker\Core\Installer;

interface InstallerInterface
    extends ManagerInterface
{
    public function install();
}