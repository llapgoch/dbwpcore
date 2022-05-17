<?php

namespace DaveBaker\Core\Layout\Handle;

/**
 * Class Manager
 * @package DaveBaker\Core\Layout\Handle
 */
class Manager extends \DaveBaker\Core\Base
{
    const HANDLE_DEFAULT = 'default';
    const HANDLE_HOMEPAGE = 'index';
    const HANDLE_LOGIN = 'login';
    const HANDLE_REGISTER = 'register';
    const HANDLE_AJAX = 'ajax';
    const HANDLE_REST = 'rest';

    /** @var string */
    protected $namespaceCode = "handle";
    /** @var array */
    protected $handles = [];
    /** @var array */
    protected $defaultHandles = [self::HANDLE_DEFAULT];

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerHandles()
    {
        $handles = [];

        $pageManager = $this->getApp()->getPageManager();

        // Add page handle
        if ($post = $pageManager->getCurrentPost()) {
            $pageRegistry = $pageManager->getPageRegistryByPageId($post->ID);

            if($pageRegistry->getId()){
                $pageSuffix = str_replace("-", "_", $pageRegistry->getPageIdentifier());
                $handles[] = $pageSuffix;

                if($this->getRequest()->isAjax()){
                    $handles[] = $pageSuffix . "_" . self::HANDLE_AJAX;
                }
            }

            $pageSuffix = str_replace("-", "_", $post->post_name);
            $handles[] = $pageSuffix;

            if($this->getRequest()->isAjax()){
                $handles[] = $pageSuffix . "_" . self::HANDLE_AJAX;
            }
        }

        if($pageManager->isOnHomepage()){
            $handles[] = self::HANDLE_HOMEPAGE;
        }

        if($pageManager->isOnLoginPage()){
            $handles[] = self::HANDLE_LOGIN;
        }

        if($pageManager->isOnRegisterPage()){
            $handles[] = self::HANDLE_REGISTER;
        }

        if($this->getRequest()->isAjax()){
            $handles[] = self::HANDLE_AJAX;
        }

        if($this->getRequest()->isRest()){
            $handles[] = self::HANDLE_REST;
        }

        $handles = array_merge($this->defaultHandles, $handles, $this->handles);

        $context = $this->fireEvent('register_handles', ['handles' => $handles]);
        $this->handles = $context->getHandles();

        return $this;
    }

    /**
     * @param $handle
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addHandle($handle)
    {
        $handle = $this->getUtilHelper()->createUrlKeyFromText($handle, '_');
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