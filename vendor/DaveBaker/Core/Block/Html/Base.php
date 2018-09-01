<?php

namespace DaveBaker\Core\Block\Html;
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
    public function _preRender()
    {
        $this->addClass($this->getDefaultClassesForElement());
        return parent::_preRender();
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultClassesForElement()
    {
        $classes = [];

        foreach($this->tagIdentifiers as $tagIdentifier){
            if($value = $this->getConfig()->getConfigValue($tagIdentifier)){
                $classes[] = $value;
            }
        }

        return $classes;
    }


    /**
     * @return ConfigInterface|mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getConfig()
    {
        if(!$this->config){
            $this->config = $this->createAppObject('\DaveBaker\Core\Config\Element');
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
            if(!isset($value, $this->tagIdentifiers)) {
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
        if(($pos = array_search($identifier, $this->tagIdentifiers) !== false)){
            unset($this->tagIdentifiers[$pos]);
        }

        return $this;
    }
}