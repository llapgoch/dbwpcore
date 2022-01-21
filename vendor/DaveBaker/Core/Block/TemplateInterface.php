<?php

namespace DaveBaker\Core\Block;

interface TemplateInterface extends BaseInterface
{
    public function setTemplate($template);
    public function getTemplate();
    public function getTemplateFile();
    public function addClass($classes);
    public function removeClass($classes);
    public function getAttributes();
    public function addAttribute($attributes);
}