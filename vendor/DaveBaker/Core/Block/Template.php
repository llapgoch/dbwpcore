<?php

namespace DaveBaker\Core\Block;

class Template
    extends \DaveBaker\Core\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $template = '';

    /** @var array */
    protected $attributes = [];

    /**
     * @return string
     * Output all registered attributes in HTML
     */
    public function getAttrs()
    {
        $attrString = '';

        return implode(' ', array_map(function($k, $v){
            return $this->escapeHtml($k) . "=" . $this->escAttr($v);
        }, array_keys($this->attributes), $this->attributes));
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
    public function addAttributes($attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render()
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