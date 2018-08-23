<?php

namespace DaveBaker\Core\Controller;

class Base extends \DaveBaker\Core\Base
{
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