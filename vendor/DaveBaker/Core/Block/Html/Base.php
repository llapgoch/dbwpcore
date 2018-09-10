<?php

namespace DaveBaker\Core\Block\Html;
use DaveBaker\Core\Api\ControllerInterface;
use DaveBaker\Core\Config\ConfigInterface;

/**
 * Class Base
 * @package DaveBaker\Core\Block\Html
 */
abstract class Base extends \DaveBaker\Core\Block\Template
{
    /** @var string  */
    protected $tagIdentifiers = [];
    /** @var ConfigInterface */
    protected $config;
    /** @var bool  */
    protected $isReplacerBlock = false;

    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $this->addClass($this->getDefaultClassesForElement());
        $this->addAttribute($this->getDefaultAttributesForElement());

        return parent::_preDispatch();
    }

    /**
     * @return bool
     */
    public function getIsReplacerBlock()
    {
        return $this->isReplacerBlock;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setIsReplacerBlock($val)
    {
        $this->isReplacerBlock = (bool) $val;
        return $this;
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultClassesForElement()
    {
        $classes = [];
        $defaultClasses = $this->getConfig()->getConfigValue('elementClasses');

        foreach($this->tagIdentifiers as $tagIdentifier){
            if(isset($defaultClasses[$tagIdentifier])){
                $classes[] = $defaultClasses[$tagIdentifier];
            }
        }

        return $classes;
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultAttributesForElement()
    {
        $attributes = [];
        $defaultAttributes = $this->getConfig()->getConfigValue('elementAttributes');

        foreach($this->tagIdentifiers as $tagIdentifier){
            if(isset($defaultAttributes[$tagIdentifier]) && is_array($defaultAttributes[$tagIdentifier])){
                $attributes = array_merge($attributes, $defaultAttributes[$tagIdentifier]);
            }
        }

        if($this->getIsReplacerBlock()){
            $attributes["data-" . ControllerInterface::BLOCK_REPLACER_KEY] = $this->getName();
        }

        return $attributes;
    }


    /**
     * @return ConfigInterface|mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getConfig()
    {
        if(!$this->config){
            $this->config = $this->createObject('\DaveBaker\Core\Config\Element');
        }

        return $this->config;
    }

    /**
     * @param $identifier
     * @return $this
     */
    protected function addTagIdentifier($identifier)
    {
        if(!is_array($identifier)){
            $identifier = [$identifier];
        }

        foreach($identifier as $value){
            if(!in_array($value, $this->tagIdentifiers)) {
                $this->tagIdentifiers[] = $value;
            }
        }


        return $this;
    }

    /**
     * @param $identifier
     * @return $this
     */
    protected function removeTagIdentifier($identifier)
    {
        // Don't put this as part of the conditional, the value comes out incorrect
        $pos = array_search($identifier, $this->tagIdentifiers);

        if($pos !== false){
           unset($this->tagIdentifiers[$pos]);
        }

        return $this;
    }

    public function getTagIdentifiers()
    {
        return $this->tagIdentifiers;
    }
}