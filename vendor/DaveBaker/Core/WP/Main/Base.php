<?php

namespace DaveBaker\Core\WP\Main;

class Base implements BaseInterface
{
    protected $app;
    protected $layouts = [];

    public function setApp(
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;
    }

    /**
     * @return \DaveBaker\Core\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param $layout
     * @return $this|void
     */
    protected function registerLayout($layout){
        if(in_array($this->layouts, $layout)){
            return;
        }

        $this->layouts[] = $layout;

        return $this;
    }
}