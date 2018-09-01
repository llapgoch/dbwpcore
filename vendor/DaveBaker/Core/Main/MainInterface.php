<?php

namespace DaveBaker\Core\Main;
/**
 * Interface MainInterface
 * @package DaveBaker\Core\Main
 */
interface MainInterface {
    public function init();
    public function registerLayouts();
    public function registerControllers();
    public function registerInstallers();
}