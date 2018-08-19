<?php

namespace DaveBaker\Core\WP\Page;

class Manager extends \DaveBaker\Core\WP\Base
{
    const DEFAULT_VALUES_CONFIG_KEY = 'defaultValues';
    const POST_AUTHOR_CONFIG_KEY = 'post_author';
    const PUBLISH_STATUS = 'publish';
    const POST_TITLE_CONFIG_KEY = 'post_title';
    const POST_CONTENT_KEY = 'post_content';
    const POST_CONTENT_SUFFIX = "content";
    const PAGE_CONTENT_SHORTCODE = "body_content";

    /** @var \DaveBaker\Core\WP\Config\ConfigInterface */
    protected $config;
    /**
     * @var array
     */
    protected $pageCache = [];
    /**
     * @var string
     */

    protected $shortcodeTags = [];
    protected $namespaceSuffix = "page_";
    protected $authorCache = [];
    protected $blocks = [];

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Option\Manager $optionManager = null,
        \DaveBaker\Core\WP\Config\ConfigInterface $config
    )
    {
        parent::__construct($app, $optionManager);
        $this->config = $config;
    }


    /**
     * @param \DaveBaker\Core\WP\Layout\Base $layout
     */
    public function registerLayout(
        \DaveBaker\Core\WP\Layout\Base $layout
    )
    {
        $layout->setManager($this);

        /** @var \DaveBaker\Core\Helper\Util $util */
        $util = $this->getApp()->getHelper('Util');

        foreach(get_class_methods($layout) as $method){
            if(preg_match("/Action$/", $method)){
                $tag = $util->camelToUnderscore($method);

                $tag = preg_replace("/_action$/", "", $tag);
                $blocks = $layout->{$method}();

                if(!isset($this->blocks[$tag])){
                    $this->blocks[$tag] = [];
                }

                if(!is_array($blocks)){
                    $blocks = [$blocks];
                }

                /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
                foreach($blocks as $block){
                    $this->blocks[$tag][$block->getName()] = $block;
                }

                add_shortcode($tag, function(){});
            }
        }
    }

    public function preDispatch()
    {
        /* TODO: Order blocks before this*/
        /**
         * @var  $tag string
         * @var  $tagBlocks array
         */
        foreach($this->blocks as $tag => $tagBlocks){
            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach($tagBlocks as $block){
                $block->preDispatch();
            }
        }
    }

    public function postDispatch()
    {
        /**
         * @var  $tag string
         * @var  $tagBlocks array
         */
        foreach($this->blocks as $tag => $tagBlocks){
            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
            foreach($tagBlocks as $block){
                $block->postDispatch();
            }
        }
    }

    /**
     * @param array $layouts
     */
    public function registerLayouts($layouts = [])
    {
        /** @var \DaveBaker\Core\WP\Layout\Base $layout */
        foreach($layouts as $layout){
            $this->registerLayout($layout);
        }
    }
    /**
     * @param $pageIdentifier
     * @param array $pageValues
     * @param bool $overwrite
     * @throws Exception
     */
    public function createPage(
        $pageIdentifier,
        $pageValues = [],
        $overwrite = false
    )
    {
        $pageValues = array_replace_recursive(
            $this->config->getConfigValue(self::DEFAULT_VALUES_CONFIG_KEY),
            $pageValues
        );

        if (!isset($pageValues[self::POST_TITLE_CONFIG_KEY]) || !$pageValues[self::POST_TITLE_CONFIG_KEY]) {
            throw new Exception("post_title not set");
        }

        $pageIdentifier = $this->optionManager->getNamespace() . $pageIdentifier;

        if ($pageId = $this->optionManager->get($pageIdentifier)) {
            /**  @var \WP_Post $post */
            $post = get_post($pageId);

            // Check if the page already exists
            if ($post && $post->post_status == self::PUBLISH_STATUS && !$overwrite) {
                return;
            }

            wp_delete_post($pageId, true);
        }

        $pageValues[self::POST_AUTHOR_CONFIG_KEY] = $this->getPostAuthorID();

        // Create page content using shortcodes
        if (!isset($pageValues[self::POST_CONTENT_KEY]) || !$pageValues[self::POST_CONTENT_KEY]) {
            $pageValues[self::POST_CONTENT_KEY] = '[' . self::PAGE_CONTENT_SHORTCODE . "]";
        }

        if (!($pageId = wp_insert_post($pageValues))) {
            throw new Exception("The post could not be created");
        }

        $this->getOptionManager()->set($pageIdentifier, $pageId);
    }

    /**
     * @param $pageIdentifier
     * @return mixed|null|void
     */
    public function pageExists(
        $pageIdentifier
    )
    {
        if ($page = $this->retreiveFromCache($pageIdentifier)) {
            return $page;
        }

        return $this->getPage($pageIdentifier);
    }

    /**
     * @param $pageIdentifier
     * @param bool $reload
     * @return null|\WP_Post
     */
    public function getPage($pageIdentifier, $reload = false)
    {
        $namespacedId = $this->app->getNamespacedOption($pageIdentifier);

        if ($reload || !($page = $this->retreiveFromCache($pageIdentifier))) {
            $page = get_post($namespacedId);
        }

        return $page;
    }

    public function registerShortcodes()
    {
        global $shortcode_tags;

        if($shortcode_tags) {
            $this->shortcodeTags = $shortcode_tags;
        }

        /*
        Render Blocks here -----------------------
        */
        foreach($this->shortcodeTags as $k => $tag){
            add_shortcode($k, function() use ($k){
                $html = "";

               /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
               foreach($this->getBlocksForShortcode($k) as $block) {
                   $html .= $block->render();
              }

                return $html;
            });
        };

    }

    /**
     * @param $shortcode string
     * @return array
     */
    protected function getBlocksForShortcode($shortcode){
        $blocks = [];
        $post = $post = $this->getCurrentPost();

        if(isset($this->blocks[$shortcode])){
            $blocks = array_merge($blocks, $this->blocks[$shortcode]);
        }

        if($post){
            $pageSuffix = str_replace("-", "_", $post->post_name);

            if(isset($this->blocks[$shortcode . "_" . $pageSuffix])){
                $blocks = array_merge($blocks, $this->blocks[$shortcode . "_" . $pageSuffix]);
            }
        }

        return $blocks;
    }

    /**
     * @return null|\WP_Post
     */
    protected function getCurrentPost()
    {
        global $post;
        return $post;
    }

    /**
     * @param $pageIdentifier
     * @return mixed|null
     */
    protected function retreiveFromCache($pageIdentifier)
    {
        $option = $this->getApp()->getNamespacedOption($pageIdentifier);

        if(isset($this->pageCache[$option])){
            return $this->pageCache[$option];
        }

        return null;
    }

    /**
     * @param $pageCode
     * @return bool
     */
    public function isOnPage($pageCode){
        if(!$this->getCurrentPost()){
            return false;
        }

        if($this->getOptionManager()->get($pageCode) == $this->post->ID){
            return true;
        }

        return false;
    }

    /**
     * @return int
     * @throws Exception
     */
    protected function getPostAuthorID()
    {
        $authorName = $this->config->getConfigValue(self::DEFAULT_VALUES_CONFIG_KEY)[self::POST_AUTHOR_CONFIG_KEY];

        /** @var \WP_User $user */
        if(!($user = get_user_by('login', $authorName))){
            throw new Exception("Post author not found {$authorName}");
        }

        return $user->ID;
    }
}