<?php

namespace DaveBaker\Core\Block\Html;

abstract class Base extends \DaveBaker\Core\Block\Template
{
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
}