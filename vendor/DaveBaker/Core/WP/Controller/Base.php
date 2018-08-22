<?php

namespace DaveBaker\Core\WP\Controller;

class Base extends \DaveBaker\Core\WP\Base
{
    /**
     * @return $this
     */
    public final function preDispatch()
    {
        $this->_preDispatch();
        return $this;
    }

    /**
     * @return $this
     */
    public final function postDispatch()
    {
        $this->_postDispatch();
        return $this;
    }

    /**
     * @return $this
     */
    public function _preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function _postDispatch()
    {
        return $this;
    }
}