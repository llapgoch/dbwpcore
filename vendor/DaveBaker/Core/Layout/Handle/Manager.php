<?php

namespace DaveBaker\Core\Layout\Handle;

/**
 * Class Manager
 * @package DaveBaker\Core\Layout\Handle
 */
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
            }

            $pageSuffix = str_replace("-", "_", $post->post_name);
            $this->addHandle($pageSuffix);
        }

        if($pageManager->isOnHomepage()){
            $this->addHandle($pageSuffix);
        }

        if($pageManager->isOnLoginPage()){
            $this->addHandle($pageSuffix);
        }

        if($pageManager->isOnRegisterPage()){
            $this->addHandle($pageSuffix);
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