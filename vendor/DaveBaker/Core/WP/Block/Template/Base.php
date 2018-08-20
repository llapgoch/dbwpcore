<?php

namespace DaveBaker\Core\WP\Block\Template;

abstract class Base
    extends \DaveBaker\Core\WP\Block\Base
    implements \DaveBaker\Core\WP\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $template = '';

    public function toHtml()
    {
        $block = $this;

        if($file = $this->getTemplateFile()){
            include $file;
        }

        return '';
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
            if(file_exists($templatePath . $this->getTemplate())){
                return $templatePath . $this->getTemplate();
            }
        }

        return "";
    }
}