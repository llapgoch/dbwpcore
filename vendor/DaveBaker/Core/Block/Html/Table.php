<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class Table
 * @package DaveBaker\Core\Block\Html
 */
class Table extends Base
{
    /** @var array  */
    protected $headers = [];
    protected $records = [];

    protected function init()
    {
        $this->setTemplate('html/table.phtml');
        $this->addTagIdentifier('table');
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