<?php

namespace DaveBaker\Core\Installer;
/**
 * Class Manager
 * @package DaveBaker\Core\Installer
 */
abstract class Base
    extends \DaveBaker\Core\Base
    implements BaseInterface
{

    /** @var string  */
    protected $namespaceCode = "installer";
    /** @var \DaveBaker\Core\Db\Query */
    protected $query;

    /** @var string */
    protected $installerCode;

    /** Override to run local installers */
    public abstract function install();

    /**
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function checkInstall()
    {
        if(!$this->installerCode){
            throw new Exception("installerNamespace not set in " . getClass($this));
        }

        /** @var $config \DaveBaker\Core\Config\Installer */
        $config = $this->getApp()->getObjectManager()->get('\DaveBaker\Core\Config\Installer');

        if(!$this->getConfigValue($this->installerCode)){
            return;
        }

        $installedVersion = $this->getOption($this->installerCode);
        $currentVersion = $config->getConfigValue($this->installerCode);

        if(!$installedVersion || version_compare($currentVersion, $installedVersion, ">")){
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