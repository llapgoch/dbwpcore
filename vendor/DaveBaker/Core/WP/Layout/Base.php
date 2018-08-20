<?php

namespace DaveBaker\Core\WP\Layout;

abstract class Base
{
    /** @var  \DaveBaker\Core\WP\Layout\Manager  */
    protected $manager;

    /**
     * @param \DaveBaker\Core\WP\Layout\Manager $manager
     */
    public function setManager(
        \DaveBaker\Core\WP\Layout\Manager $manager
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
     * @return \DaveBaker\Core\WP\Layout\Manager
     */
    public function getManager()
    {
        return $this->manager;
    }
}