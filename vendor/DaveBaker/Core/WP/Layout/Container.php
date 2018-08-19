<?php

namespace DaveBaker\Core\WP\Layout;

class Container
{
    /** @var  \DaveBaker\Core\WP\Layout\Base */
    protected $layout;
    /**
     * @var string
     */
    protected $tag = '';
    /**
     * @var string
     */
    protected $method = '';

    public function setLayout(\DaveBaker\Core\WP\Layout\Base $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return Base
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

}