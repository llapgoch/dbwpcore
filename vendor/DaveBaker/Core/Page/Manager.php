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
     * @param string $pageIdentifier
     * @param array $pageValues
     * @param bool $overwrite
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createPage(
        $pageIdentifier,
        $pageValues = [],
        $overwrite = false
    ) {

        $pageValues = array_replace_recursive(
            $this->config->getConfigValue(self::DEFAULT_VALUES_CONFIG_KEY),
            $pageValues
        );

        if (!isset($pageValues[self::POST_TITLE_CONFIG_KEY]) || !$pageValues[self::POST_TITLE_CONFIG_KEY]) {
            throw new Exception("post_title not set");
        }

        $pageRegistry = $this->getPageRegistryByPageIdentifier($pageIdentifier);

        if ($pageRegistry->getId()) {
            /**  @var \WP_Post $post */
            $post = get_post($pageRegistry->getPageId());

            // Check if the page already exists
            if ($post && $post->post_status == self::PUBLISH_STATUS && !$overwrite) {
                return $this;
            }

            wp_delete_post($pageRegistry->getPageId(), true);
        }

        $pageValues[self::POST_AUTHOR_CONFIG_KEY] = $this->getPostAuthorID();

        // Create page content using shortcodes
        if (!isset($pageValues[self::POST_CONTENT_KEY]) || !$pageValues[self::POST_CONTENT_KEY]) {
            $pageValues[self::POST_CONTENT_KEY] = '[' . self::PAGE_CONTENT_SHORTCODE . "]";
        }

        if (!($pageId = wp_insert_post($pageValues))) {
            throw new Exception("The post could not be created");
        }

        $pageRegistry
            ->setPageIdentifier($pageIdentifier)
            ->setPageId($pageId)
            ->setOptionCode($this->getNamespacedOption($pageIdentifier))
            ->save();

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
        $page = null;
        $pageRegistry = $this->getPageRegistryByPageIdentifier($pageIdentifier);

        if(!$pageRegistry->getId()){
            return false;
        }

        if ($reload || !($page = $this->retrieveFromCache($pageIdentifier))) {
            if($page = get_post($pageRegistry->getPageId())){
                $this->pageCache[$pageIdentifier] = $page;
            }
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
     * @param $pageIdentifier
     * @return bool
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isOnPage($pageIdentifier){
        if(!($post = $this->getCurrentPost())){
            return false;
        }

        $pageRegistry = $this->getPageRegistryByPageIdentifier($pageIdentifier);

        if(!$pageRegistry->getId()){
            return false;
        }

        return $post->ID == $pageRegistry->getPageId();
    }

    /**
     * @param $pageIdentifier
     * @return false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrl($pageIdentifier, $params = [], $returnUrl = null)
    {
        $pageRegistry = $this->getPageRegistryByPageIdentifier($pageIdentifier);

        if($returnUrl === true){
            $returnUrl = $this->getUrlHelper()->getCurrentUrl();
        }

        if($returnUrl){
            $params[\DaveBaker\Core\App\Request::RETURN_URL_PARAM] = $this->getApp()->getRequest()->createReturnUrlParam($returnUrl);
        }

        if($id = $pageRegistry->getPageId()){
            return add_query_arg($params, get_permalink($id));
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
     * @throws \DaveBaker\Core\Object\Exception
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
     * @throws \DaveBaker\Core\Object\Exception
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

        $pages = (array)$pages;

        if(!isset($GLOBALS['pagenow'])){
            return false;
        }
        
        return in_array($GLOBALS['pagenow'], $pages);
    }

    /**
     * @param $pageIdentifier
     * @return \DaveBaker\Core\Model\Db\Page\Registry
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageRegistryByPageIdentifier($pageIdentifier)
    {
        /** @var \DaveBaker\Core\Model\Db\Page\Registry $registry */
        $registry = $this->createAppObject('\DaveBaker\Core\Model\Db\Page\Registry');
        return $registry->load($pageIdentifier, 'page_identifier');
    }

    /**
     * @param int $pageId
     * @return \DaveBaker\Core\Model\Db\Page\Registry
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageRegistryByPageId($pageId)
    {
        /** @var \DaveBaker\Core\Model\Db\Page\Registry $registry */
        $registry = $this->createAppObject('\DaveBaker\Core\Model\Db\Page\Registry');
        return $registry->load($pageId, 'page_id');
    }
}