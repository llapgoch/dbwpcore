<?php

namespace DaveBaker\Core\Layout\Handle;

class Manager extends \DaveBaker\Core\Base
{
    /** @var string */
    protected $namespaceCode = "handle";
    
    /** @var array */
    protected $handles = [];
    
    /** @var array */
    protected $defaultHandles = ['default'];

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     */
    public function registerHandles()
    {
        $this->handles = $this->defaultHandles;

        $pageManager = $this->getApp()->getPageManager();

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

        $context = $this->fireEvent('register_handles', ['handles' => $this->handles]);
        $this->handles = $context->getHandles();

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