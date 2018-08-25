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
     */
    public final function postDispatch()
    {
        $this->fireEvent('postdispatch_before');
        $this->_postDispatch();
        $this->fireEvent('postdispatch_after');
        return $this;
    }

    /**
     * @return \DaveBaker\Core\App\Request
     */
    public function getRequest()
    {
        return $this->getApp()->getRequest();
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