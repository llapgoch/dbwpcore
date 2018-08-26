<?php

namespace DaveBaker\Core\Controller;
/**
 * Class Base
 * @package DaveBaker\Core\Controller
 */
class Base extends \DaveBaker\Core\Base
{
    /** @var string */
    protected $namespaceCode = 'controller';

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function preDispatch()
    {
        $this->fireEvent('predispatch_before');
        $this->_preDispatch();
        $this->fireEvent('predispatch_after');
        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function postDispatch()
    {
        $this->fireEvent('postdispatch_before');
        $this->_postDispatch();
        $this->fireEvent('postdispatch_after');
        return $this;
    }

    /**
     * @return \DaveBaker\Core\App\Request|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRequest()
    {
        return $this->getApp()->getRequest();
    }

    /**
     * @return \DaveBaker\Core\App\Response|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getResponse()
    {
        return $this->getApp()->getResponse();
    }

    /**
     * @param $url
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirect($url)
    {
        return $this->getResponse()->redirect($url);
    }

    /**
     * @param $pageIdentifier
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier)
    {
        return $this->getResponse()->redirectToPage($pageIdentifier);
    }

    /**
     * @return $this
     */
    protected function _preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _postDispatch()
    {
        return $this;
    }
}