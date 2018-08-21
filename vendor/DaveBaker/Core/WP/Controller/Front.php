<?php

/**
 * Example for listening to page events
 *
 *   Event = wordpressAction + page_name
 *   $this->getEventManager()->register("wp_hello-world", function(){
 *     var_dump("woo");
 *   });
 */

namespace DaveBaker\Core\WP\Controller;

class Front extends \DaveBaker\Core\WP\Base
{
    /** @var  WP_Post */
    protected $post;
    protected $namespaceSuffix = "controller_";

    // Add more wordpress events here as required
    protected $eventRegisters = [
        'wp'
    ];

    public function __construct(
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Option\Manager $optionManager = null
    ){
        parent::__construct($app, $optionManager);
        
        $this->addEvents();
    }

    protected function addEvents()
    {
        foreach($this->eventRegisters as $eventRegister) {
            add_action($eventRegister, [
                $this,
                'fireEventForPost',
            ]);
        }
    }

    public function fireEventForPost()
    {
        global $post;

        if(!$this->post) {
            $this->post = $post;
        }

        if($this->post){
            $this->getEventManager()->fire(current_action() . "_" . $this->post->post_name);
            $this->getEventManager()->fire(current_action() . "_" . $this->post->ID);
        }

        return $this;
    }
}