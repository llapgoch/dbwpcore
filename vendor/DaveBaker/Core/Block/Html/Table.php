<?php

namespace DaveBaker\Core\Block\Html;
use DaveBaker\Core\Block\Exception;
use DaveBaker\Core\Definitions\General;
use DaveBaker\Core\Definitions\Table as TableDefinition;

/**
 * Class Table
 * @package DaveBaker\Core\Block\Html
 */
class Table extends Base
{
    const SORTABLE_COLUMNS_DATA_KEY = 'sortable_columns';
    const ORDER_DESC = 'DESC';
    const ORDER_ASC = 'ASC';

    /** @var array  */
    protected $headers = [];
    /** @var array  */
    protected $records = [];
    /** @var bool  */
    protected $jsUpdater = true;
    /** @var bool  */
    protected $isReplacerBlock = true;
    /** @var string  */
    protected $orderColumn = '';
    /** @var string  */
    protected $orderDir = 'ASC';
    /** @var \DaveBaker\Core\Block\Components\Paginator */
    protected $paginator;

    /**
     * @return Base|void
     */
    protected function _construct()
    {
        $this->setTemplate('html/table.phtml');
        $this->addTagIdentifier('table');
    }

    /**
     * @param $paginator
     * @return $this
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @return \DaveBaker\Core\Block\Components\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param $column
     * @param string $dir
     * @return $this
     * @throws Exception
     */
    public function setColumnOrder($column, $dir = 'ASC')
    {
        if(!$column){
            $this->orderColumn = '';
            return $this;
        }

        if(!in_array($column, $this->getHeaderKeys())){
            throw new Exception('Column does not exist');
        }

        $this->orderColumn = $column;
        $this->orderDir = strtoupper($dir);

        return $this;
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
    public function getThClasses($header)
    {
        $columns = $this->getSortableColumns();
        $classes = [];

        if(isset($columns[$header])){
            $ascClass = $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_ASC_CLASS);
            $descClass = $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_DESC_CLASS);
            $classes[] = $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_CLASS);

            if(in_array(TableDefinition::HEADER_SORTABLE_ALPHA, $columns[$header])){
                $classes[] = $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_ALPHA_CLASS);
            }

            if($this->jsUpdater){
                $classes[] = $this->getConfig()->getConfigValue(TableDefinition::CONFIG_SORTABLE_TH_JS_CLASS);
            }

            if(in_array( TableDefinition::HEADER_SORTABLE_ASC, $columns[$header])){
                $classes[] = $ascClass;
            } else {
                if (in_array(TableDefinition::HEADER_SORTABLE_DESC, $columns[$header])) {
                    $classes[] = $descClass;
                }
            }

            if($this->orderColumn == $header){
                if($this->orderDir == self::ORDER_ASC){
                    $classes[] = $ascClass;
                }else{
                    $classes[] = $descClass;
                }
            }
        }

        return $this->makeClassString($classes);
    }

    /**
     * @param $header
     * @param bool $includeClasses
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getThAttrs($header, $includeClasses = true)
    {
        $attributes = [
            TableDefinition::ELEMENT_DATA_KEY_COLUMN_ID => $header
        ];

        $attrs = $this->makeAttrs($attributes);

        if($includeClasses){
            $attrs .= $this->getThClasses($header);
        }

        return $attrs;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function addSortableColumns($columns)
    {
        $columns = (array)$columns;

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
        $headers = (array)$headers;

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