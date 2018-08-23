<?php

namespace DaveBaker\Core\Main;

interface MainInterface extends BaseInterface{
    public function init();
    public function registerLayouts();
    public function registerControllers();
}