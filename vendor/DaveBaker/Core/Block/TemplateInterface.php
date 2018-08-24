<?php

namespace DaveBaker\Core\Block;

interface TemplateInterface extends BaseInterface
{
    public function setTemplate($template);
    public function getTemplate();
    public function getTemplateFile();
}