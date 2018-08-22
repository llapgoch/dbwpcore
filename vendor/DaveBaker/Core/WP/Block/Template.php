<?php

namespace DaveBaker\Core\WP\Block;

class Template
    extends \DaveBaker\Core\WP\Block\Base
    implements \DaveBaker\Core\WP\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $template = '';

    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        if(!$file = $this->getTemplateFile()){
            throw new Exception("Template file not set or not found for {$this->getName()}");
        }

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
            if(file_exists($templatePath . $this->getTemplate())){
                return $templatePath . $this->getTemplate();
            }
        }

        return "";
    }
}