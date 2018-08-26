<?php

namespace DaveBaker\Core\Installer;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
class CoreApplication
    extends Base
    implements InstallerInterface
{
    /** @var string */
    protected $installerCode = 'core_application';

    /** Override to run local installers */
    public function install()
    {
        exit;
        // TODO: Implement install() method.
    }
}