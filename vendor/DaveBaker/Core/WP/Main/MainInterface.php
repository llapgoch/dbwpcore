<?php

namespace DaveBaker\Core\WP\Main;

interface MainInterface extends BaseInterface{
    public function init();
    public function registerLayouts();
    public function registerControllers();
}