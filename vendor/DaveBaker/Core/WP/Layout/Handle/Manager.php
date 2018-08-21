<?php

namespace DaveBaker\Core\WP\Layout\Handle;

class Manager extends \DaveBaker\Core\WP\Base
{
    protected $handles = [];
    protected $defaultHandles = ['default'];

    /**
     * @return $this
     * @throws \DaveBaker\WP\Event\Exception
     */
    public function registerHandles()
    {
        $this->handles = $this->defaultHandles;

        $pageManager = $this->getApp()->getPageManager();

        if($handles = $this->getEventManager()->fire('register_handles')){
            array_merge($this->handles, $handles);
        }

        // Add page handle
        $post = $pageManager->getCurrentPost();

        if ($post) {
            $pageSuffix = str_replace("-", "_", $post->post_name);
            $this->handles[] = $pageSuffix;
        }

        if($pageManager->isOnHomepage()){
            $this->handles[] = "index";
        }

        if($pageManager->isOnLoginPage()){
            $this->handles[] = 'login';
        }

        if($pageManager->isOnRegisterPage()){
            $this->handles[] = 'register';
        }
        
        return $this;
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return $this->handles;
    }
}