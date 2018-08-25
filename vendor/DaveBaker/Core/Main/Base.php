<?php

namespace DaveBaker\Core\Main;

/**
 * Class Base
 * @package DaveBaker\Core\Main
 */
class Base implements BaseInterface
{
    /** @var  \DaveBaker\Core\App */
    protected $app;
    /** @var array  */
    protected $layouts = [];

    /**
     * @param \DaveBaker\Core\App $app
     * @return $this
     */
    public function setApp(
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;
        return $this;
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