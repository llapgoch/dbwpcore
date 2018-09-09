<?php

namespace DaveBaker\Core\Layout\Handle;

/**
 * Class Manager
 * @package DaveBaker\Core\Layout\Handle
 */
class Manager extends \DaveBaker\Core\Base
{
    const HANDLE_HOMEPAGE = 'index';
    const HANDLE_LOGIN = 'login';
    const HANDLE_REGISTER = 'register';
    const HANDLE_AJAX = 'ajax';

    /** @var string */
    protected $namespaceCode = "handle";
    /** @var array */
    protected $handles = [];
    /** @var array */
    protected $defaultHandles = ['default'];

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerHandles()
    {
        $this->handles = $this->defaultHandles;

        $pageManager = $this->getApp()->getPageManager();

        // Add page handle
        if ($post = $pageManager->getCurrentPost()) {
            $pageRegistry = $pageManager->getPageRegistryByPageId($post->ID);

            if($pageRegistry->getId()){
                $pageSuffix = str_replace("-", "_", $pageRegistry->getPageIdentifier());
                $this->addHandle($pageSuffix);

                if($this->getRequest()->isAjax()){
                    $this->addHandle($pageSuffix . "_" . self::HANDLE_AJAX);
                }
            }

            $pageSuffix = str_replace("-", "_", $post->post_name);
            $this->addHandle($pageSuffix);

            if($this->getRequest()->isAjax()){
                $this->addHandle($pageSuffix . "_" . self::HANDLE_AJAX);
            }
        }

        if($pageManager->isOnHomepage()){
            $this->addHandle(self::HANDLE_HOMEPAGE);
        }

        if($pageManager->isOnLoginPage()){
            $this->addHandle(self::HANDLE_LOGIN);
        }

        if($pageManager->isOnRegisterPage()){
            $this->addHandle(self::HANDLE_REGISTER);
        }

        if($this->getRequest()->isAjax()){
            $this->addHandle(self::HANDLE_AJAX);
        }

        $context = $this->fireEvent('register_handles', ['handles' => $this->handles]);
        $this->handles = $context->getHandles();

        return $this;
    }

    /**
     * @param string $handle
     * @return $this
     */
    protected function addHandle($handle)
    {
        if(!in_array($handle, $this->handles)){
            $this->handles[] = $handle;
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