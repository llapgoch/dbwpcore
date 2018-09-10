<?php

namespace DaveBaker\Core\Block\Html;
use DaveBaker\Core\Definitions\General;
use DaveBaker\Core\Definitions\Table as TableDefinition;

/**
 * Class Table
 * @package DaveBaker\Core\Block\Html
 */
class Table extends Base
{
    const SORTABLE_COLUMNS_DATA_KEY = 'sortable_columns';
    /** @var array  */
    protected $headers = [];
    /** @var array  */
    protected $records = [];
    /** @var bool  */
    protected $jsUpdater = true;

    protected function init()
    {
        $this->setTemplate('html/table.phtml');
        $this->addTagIdentifier('table');
    }

    /**
     * @return Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        if($this->getSortableColumns()){
            $this->addClass(
                $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TABLE_CLASS)
            );
        }

        if($this->jsUpdater){
            $this->addClass(
                $this->getConfig()->getConfigValue(TableDefinition::CONFIG_TABLE_UPDATER_JS_CLASS)
            );
        }

        parent::_preRender();
    }

    /**
     * @param $header
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getThClass($header)
    {
        $columns = $this->getSortableColumns();
        $class = '';

        if(isset($columns[$header])){
            $class .= $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_CLASS);

            if(in_array(TableDefinition::HEADER_SORTABLE_ALPHA, $columns[$header])){
                $class .= " " . $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_ALPHA_CLASS);
            }

            if($this->jsUpdater){
                $class .= " " . $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_JS_CLASS);
            }

            if(in_array( TableDefinition::HEADER_SORTABLE_ASC, $columns[$header])){
                $class .= " " . $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_ASC_CLASS);
            } else {
                if (in_array(TableDefinition::HEADER_SORTABLE_DESC, $columns[$header])) {
                    $class .= " " . $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_DESC_CLASS);
                }
            }
        }

        return $class;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function addSortableColumns($columns)
    {
        if(!is_array($columns)){
            $columns = [$columns];
        }

        $this->setData(self::SORTABLE_COLUMNS_DATA_KEY,
            array_merge_recursive($columns, $this->getSortableColumns())
        );

        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getSortableColumns()
    {
        if(!$this->getData(self::SORTABLE_COLUMNS_DATA_KEY)){
            $this->setData(self::SORTABLE_COLUMNS_DATA_KEY, []);
        }

        return $this->getData(self::SORTABLE_COLUMNS_DATA_KEY);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaderKeys()
    {
        return array_keys($this->headers);
    }

    /**
     * @param array|string $headers
     * @return $this
     */
    public function removeHeader($headers)
    {
        if(!is_array($headers)){
            $headers = [$headers];
        }

        foreach($headers as $header) {
            if (isset($this->headers[$header])) {
                unset($this->headers[$header]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param $records
     * @return $this
     */
    public function setRecords($records)
    {
        $this->records = $records;
        return $this;
    }
}