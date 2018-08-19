<?php

namespace DaveBaker\Core\WP\Layout;

abstract class Base
{
    /** @var  \DaveBaker\Core\WP\Page\Manager  */
    protected $manager;

    /**
     * @param \DaveBaker\Core\WP\Page\Manager $manager
     */
    public function setManager(
        \DaveBaker\Core\WP\Page\Manager $manager
    ) {
        $this->manager = $manager;
    }

    /**
     * @return \DaveBaker\Core\App
     */
    public function getApp()
    {
        return $this->getManager()->getApp();
    }

    /**
     * @return \DaveBaker\Core\WP\Page\Manager
     */
    public function getManager()
    {
        return $this->manager;
    }
}