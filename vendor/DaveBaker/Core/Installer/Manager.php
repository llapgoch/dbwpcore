<?php

namespace DaveBaker\Core\Installer;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
abstract class Manager
    extends \DaveBaker\Core\Base
    implements ManagerInterface
{
    const VERSION_OPTION = 'version';

    /** @var string  */
    protected $namespaceCode = "installer";
    /** @var \DaveBaker\Core\Db\Query */
    protected $query;

    /** Override to run local installers */
    public abstract function install();

    public function checkInstall()
    {
        /** @var $config \DaveBaker\Core\Config\Installer */
        $config = $this->getApp()->getObjectManager()->get('\DaveBaker\Core\Config\Installer');
        $installedVersion = $this->getOption(self::VERSION_OPTION);
        $currentVersion = $config->getConfigValue(self::VERSION_OPTION);

        if(version_compare($currentVersion, $installedVersion, ">")){
            try {
                $this->install();

                // Upgrade the version in the database
                $this->setOption(self::VERSION_OPTION, $currentVersion);
            }catch (\Exception $e){
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * @return \DaveBaker\Core\Db\Query
     */
    protected function getQuery()
    {
        if(!$this->query) {
            $this->query = $this->createAppObject('\DaveBaker\Core\Db\Query');
        }

        return $this->query;
    }

    /**
     * @param $tableName string
     * @return string
     */
    protected function getTableName($tableName)
    {
        return $this->getApp()->getHelper('Db')->getTableName($tableName);
    }

    /**
     * @param $tableName string
     * @param $schema string
     * @return $this
     * @throws \DaveBaker\Core\Db\Exception
     */
    protected function deltaTable($tableName, $schema)
    {
        /** @var \DaveBaker\Core\Db\Query $query */
        $this->getQuery()->deltaTable($tableName, $schema);
        return $this;
    }
}