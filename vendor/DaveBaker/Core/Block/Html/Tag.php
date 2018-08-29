<?php

namespace DaveBaker\Core\Block\Html;

class Tag extends Base
{
    /** @var  */
    protected $tag = 'div';

    protected function init()
    {
        $this->setTemplate('html/tag.phtml');
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
}