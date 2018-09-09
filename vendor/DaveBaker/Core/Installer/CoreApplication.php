<?php

namespace DaveBaker\Core\Installer;

use DaveBaker\Core\Definitions\General as GeneralDefinition;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
class CoreApplication
    extends Base
    implements InstallerInterface
{
    /** @var string */
    protected $installerCode = 'core_application';

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $this->getQuery()->deltaTable(
            'page_registry',
            "CREATE TABLE `{{tableName}}` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `page_identifier` varchar(255) DEFAULT NULL,
              `page_id` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `page_identifier` (`page_identifier`),
              KEY `page_id` (`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        $this->getApp()->getPageManager()->createPage(
            GeneralDefinition::ROUTE_PAGE_ID,
            [
                "post_title" => "DBWP_CORE_ROUTER"
            ]
        );
    }
}