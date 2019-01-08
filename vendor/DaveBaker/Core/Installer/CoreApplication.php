<?php

namespace DaveBaker\Core\Installer;

use DaveBaker\Core\Definitions\General as GeneralDefinition;
use DaveBaker\Core\Definitions\Roles;
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
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `page_identifier` (`page_identifier`),
              KEY `page_id` (`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        $this->getQuery()->deltaTable(
            'file_upload',
            "CREATE TABLE `{{tableName}}` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `filename` varchar(255) DEFAULT NULL,
              `extension` varchar(20) DEFAULT NULL,
              `upload_type` varchar(20) DEFAULT NULL,
              `parent_id` INT(11) DEFAULT NULL,
              `mime_type` VARCHAR(255) DEFAULT NULL,
              `file_parent_id` INT(11) DEFAULT NULL,
              `file_hash` VARCHAR(255) DEFAULT NULL,
              `temporary_id` VARCHAR(255) DEFAULT NULL,
              `created_by_id` INT(11) DEFAULT NULL,
              `last_updated_by_id` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              `is_deleted` INT(1) DEFAULT 0,
              PRIMARY KEY (`id`),
              KEY `file_hash` (`file_hash`),
              KEY `temporary_id` (`temporary_id`),
              KEY `upload_type_parent` (`upload_type`, `parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_bin;"
        );

        // Allow administrators to upload files by default
        $this->getUserHelper()->addCapability(
            Roles::ROLE_ADMINISTRATOR,
            Roles::CAP_UPLOAD_FILE_ADD,
            true,
            false
        );

        $this->getUserHelper()->addCapability(
            Roles::ROLE_ADMINISTRATOR,
            Roles::CAP_UPLOAD_FILE_REMOVE,
            true,
            false
        );

        $this->getUserHelper()->addCapability(
            Roles::ROLE_ADMINISTRATOR,
            Roles::CAP_UPLOAD_FILE_REMOVE_ANY,
            true,
            false
        );

    }
}