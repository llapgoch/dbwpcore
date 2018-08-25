<?php

namespace DaveBaker\Core\Page;
/**
 * Class Manager
 * @package DaveBaker\Core\Page
 */
class Manager extends \DaveBaker\Core\Base
{
    const DEFAULT_VALUES_CONFIG_KEY = 'defaultValues';
    const POST_AUTHOR_CONFIG_KEY = 'post_author';
    const PUBLISH_STATUS = 'publish';
    const POST_TITLE_CONFIG_KEY = 'post_title';
    const POST_CONTENT_KEY = 'post_content';
    const POST_CONTENT_SUFFIX = "content";
    const PAGE_CONTENT_SHORTCODE = "body_content";
    // action is used in the querystring on pages like register
    const ACTION_PARAM = 'action';
    const LOGIN_REGISTER_PARAM_VALUE = 'register';

    /** @var string */
    protected $namespaceCode = 'page';
    /** @var \DaveBaker\Core\Config\ConfigInterface */
    protected $config;
    /** @var array */
    protected $pageCache = [];
    /** @var array */
    protected $authorCache = [];

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\Config\ConfigInterface $config
    ) {
        parent::__construct($app);
        $this->config = $config;
    }
    
    /**
     * @param $pageIdentifier
     * @param array $pageValues
     * @param bool $overwrite
     * @throws Exception
     * @return $this
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

        if ($pageId = $this->getOption($pageIdentifier)) {
            /**  @var \WP_Post $post */
            $post = get_post($pageId);

            // Check if the page already exists
            if ($post && $post->post_status == self::PUBLISH_STATUS && !$overwrite) {
                return $this;
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

        $this->setOption($pageIdentifier, $pageId);

        return $this;
    }

    /**
     * @param $pageIdentifier
     * @return bool
     */
    public function pageExists($pageIdentifier)
    {
        if ($page = $this->retrieveFromCache($pageIdentifier)) {
            return $page;
        }

        return (bool) $this->getPage($pageIdentifier);
    }

    /**
     * @param $pageIdentifier
     * @param bool $reload
     * @return null|\WP_Post
     */
    public function getPage($pageIdentifier, $reload = false)
    {
        $namespacedId = $this->getApp()->getNamespacedOption($pageIdentifier);

        if ($reload || !($page = $this->retrieveFromCache($pageIdentifier))) {
            $page = get_post($namespacedId);
        }

        return $page;
    }

    /**
     * @return null|\WP_Post
     */
    public function getCurrentPost()
    {
        global $post;
        return $post;
    }

    /**
     * @param $pageIdentifier
     * @return mixed|null
     */

    protected function retrieveFromCache($pageIdentifier)
    {
        if(isset($this->pageCache[$pageIdentifier])){
            return $this->pageCache[$pageIdentifier];
        }

        return null;
    }

    /**
     * @param $pageCode
     * @return bool
     */
    public function isOnPage($pageCode){
        if(!($post = $this->getCurrentPost())){
            return false;
        }

        if($this->getOption($pageCode) == $post->ID){
            return true;
        }

        return false;
    }

    /**
     * @param $pageIdentifier
     * @return false|string
     */
    public function getUrl($pageIdentifier)
    {
        if($option = $this->getOption($pageIdentifier)){
            return get_permalink($option);
        }

        return "";
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

    /**
     * @return bool
     */
    public function isOnLoginPage(){
        global $GLOBALS;

        if(!isset($GLOBALS['pagenow'])){
            return false;
        }

        $actionParam = $this->getApp()->getRequest()->getParam(self::ACTION_PARAM);

        if($actionParam == self::LOGIN_REGISTER_PARAM_VALUE){
            return false;
        }

        return $this->checkPageNow(['wp-login.php', 'wp-register.php']);
    }

    /**
     * @return bool
     */
    public function isOnRegisterPage(){
        if($this->checkPageNow('wp-register.php')){
            return true;
        }

        $actionParam = $this->getApp()->getRequest()->getParam(self::ACTION_PARAM);

        if($this->checkPageNow('wp-login.php')
            && $actionParam == self::LOGIN_REGISTER_PARAM_VALUE){

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isOnHomepage()
    {
        return is_home();
    }

    /**
     * @param $pages string|array
     * @return bool
     */
    public function checkPageNow($pages)
    {
        global $GLOBALS;

        if(!is_array($pages)){
            $pages = [$pages];
        }

        if(!isset($GLOBALS['pagenow'])){
            return false;
        }
        
        return in_array($GLOBALS['pagenow'], $pages);
    }
}