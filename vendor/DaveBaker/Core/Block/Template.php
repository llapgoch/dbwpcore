<?php

namespace DaveBaker\Core\Block;
use DaveBaker\Core\Api\ControllerInterface;
use DaveBaker\Core\Config\ConfigInterface;

/**
 * Class Template
 * @package DaveBaker\Core\Block
 */
class Template
    extends \DaveBaker\Core\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    const JS_DATA_ITEMS_KEY = 'js_data_items';
    /** @var string  */
    protected $template = '';
    /** @var array */
    protected $attributes = [];
    /** @var array  */
    protected $classes = [];
    /** @var ConfigInterface */
    protected $config;
    /**
     * @var bool
     * Replacer blocks are automatically picked up and replaced when performing JS requests
     */
    protected $isReplacerBlock = false;
    /** @var string  */
    protected $jsDataKey = 'data-js-data'; // key for the element attribute
    /** @var string  */
    protected $tagIdentifiers = [];

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function preDispatch()
    {
        $this->addClass($this->getDefaultClassesForElement());
        $this->addAttribute($this->getDefaultAttributesForElement());
        parent::preDispatch();
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
     * @param $items
     * @return $this
     *
     * Add items to be output as attribute defined in jsDataKey
     */
    public function addJsDataItems($items)
    {
        $this->setJsDataItems(array_replace_recursive($this->getJsDataItems(), $items));
        return $this;
    }

    /**
     * @param $items
     * @return $this
     */
    public function setJsDataItems($items)
    {
        $this->setData(self::JS_DATA_ITEMS_KEY, $items);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getJsDataItems()
    {
        if(!is_array($this->getData(self::JS_DATA_ITEMS_KEY))){
            $this->setData(self::JS_DATA_ITEMS_KEY, []);
        }

        return $this->getData(self::JS_DATA_ITEMS_KEY);
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
     * @return array|string
     */
    public function getReplacerAttribute()
    {
        if(!$this->getIsReplacerBlock()){
            return [];
        }

        return ['data-' . ControllerInterface::BLOCK_REPLACER_KEY => $this->getName()];
    }

    /**
     * @param bool $includeClass
     * @return string
     * Output all registered attributes and classes in HTML
     */
    public function getAttrs($includeClass = true, $includeReplacer = true)
    {
        if(!$includeReplacer){
            $this->removeAttribute("data-" . ControllerInterface::BLOCK_REPLACER_KEY);
        }

        $attributes = array_replace_recursive($this->getAttributes(), $this->getReplacerAttribute());

        if($jsDataItems = $this->getJsDataItems()) {
            $attributes[$this->jsDataKey] = json_encode($jsDataItems);
        }

        $attrString = $this->makeAttrs($attributes);

        if($includeClass && $this->classes){
            $attrString .= $this->makeClassString($this->getClasses());
        }

        return $attrString;
    }

    /**
     * @param array $attrs
     * @return string
     */
    public function makeAttrs($attrs)
    {
        if(!is_array($attrs)){
            $attrs = [$attrs];
        }

        $attrString  = implode(' ', array_map(function($k, $v){
            return $this->escapeHtml($k) . "=\"" . $this->escAttr($v) . "\"";
        }, array_keys($attrs), $attrs));

        return $attrString;
    }

    public function makeClassString($classes)
    {
        return " class='" . $this->makeClassValueString($classes) . "'";
    }

    /**
     * @param $classes
     * @return string
     */
    protected function makeClassValueString($classes)
    {
       if(!is_array($classes)){
           $classes = [$classes];
       }

       return trim(implode(" ", array_map([$this, 'escAttr'], $classes)));
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param $classes array|string
     * @return $this
     */
    public function addClass($classes)
    {
        if(!is_array($classes)){
            $classes = explode(" ", $classes);
        }

        foreach ($classes as $class){
            if(!in_array((string) $class, $this->classes)) {
                $this->classes[] = (string)$class;
            }
        }

        return $this;
    }

    /**
     * @param $classes array|string
     * @return $this
     */
    public function removeClass($classes)
    {
        if(!is_array($classes)){
            $classes = [$classes];
        }

        foreach($this->classes as $k => $class){
            if(in_array($class, $classes)){
                unset($this->classes[$k]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * @param array $attributes
     * @return $this
     *
     * An array of key/value pairs of name/value
     */
    public function addAttribute($attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function removeAttribute($key)
    {
        if(!is_array($key)){
            $key = [$key];
        }

        foreach($key as $k) {
            if (isset($this->attributes[$k])) {
                unset($this->attributes[$k]);
            }
        }

        return $this;
    }


    /**
     * @param $identifier
     * @return $this
     */
    public function addTagIdentifier($identifier)
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
    public function removeTagIdentifier($identifier)
    {
        // Don't put this as part of the conditional, the value comes out incorrect
        $pos = array_search($identifier, $this->tagIdentifiers);

        if($pos !== false){
            unset($this->tagIdentifiers[$pos]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTagIdentifiers()
    {
        return $this->tagIdentifiers;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _render()
    {
        if(!$this->getTemplate()){
            throw new Exception("Template file not set for {$this->getName()}");
        }

        if(!$file = $this->getTemplateFile()){
            throw new Exception("Template file not found for {$this->getName()}");
        }

        $this->rendered = true;
        ob_start();

        // Define items for use in templates here
        $block = $this;
        include $file;

        return ob_get_clean();
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTemplateFile()
    {
        $templatePaths = $this->app->getLayoutManager()->getTemplatePaths();

        foreach($templatePaths as $templatePath){
            if(file_exists($templatePath . DS . $this->getTemplate())){
                return $templatePath . DS . $this->getTemplate();
            }
        }

        return "";
    }

}