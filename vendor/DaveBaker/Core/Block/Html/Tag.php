<?php

namespace DaveBaker\Core\Block\Html;

class Tag extends Base
{
    /** @var  */
    protected $tag = 'div';

    /**
     * @return Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('html/tag.phtml');
    }

    /**
     * @param $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->removeTagIdentifier($this->getTag());
        $this->tag = $tag;
        $this->addTagIdentifier($tag);
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