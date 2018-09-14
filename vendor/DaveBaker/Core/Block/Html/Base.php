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


    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addClass($this->getDefaultClassesForElement());
        $this->addAttribute($this->getDefaultAttributesForElement());
        parent::_construct();
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultClassesForElement()
    {
        return $this->getDefaultClassesForIdentifiers($this->getTagIdentifiers());
    }

    /**
     * @param $identifiers
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultClassesForIdentifiers($identifiers)
    {
        $classes = [];
        $defaultClasses = $this->getConfig()->getConfigValue('elementClasses');

        foreach($identifiers as $tagIdentifier){
            if(isset($defaultClasses[$tagIdentifier])){
                $classes[] = $defaultClasses[$tagIdentifier];
            }
        }

        return $classes;
    }

    /**
     * @param $identifiers
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultAttributesForIdentifiers($identifiers)
    {
        $attributes = [];
        $identifiers = (array)$identifiers;
        $defaultAttributes = $this->getConfig()->getConfigValue('elementAttributes');

        foreach($identifiers as $identifier){
            if(isset($defaultAttributes[$identifier]) && is_array($defaultAttributes[$identifier])){
                $attributes = array_merge($attributes, $defaultAttributes[$identifier]);
            }
        }

        return $attributes;
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultAttributesForElement()
    {
        $attributes = $this->getDefaultAttributesForIdentifiers($this->getTagIdentifiers());

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