<?php

namespace DaveBaker\Core\Block\Components;
/**
 * Class Paginator
 * @package DaveBaker\Core\Block\Components
 */
class Paginator
    extends \DaveBaker\Core\Block\Html\Base
{
    /** @var int  */
    protected $page = 1;
    /** @var int  */
    protected $recordsPerPage = 10;
    /** @var int  */
    protected $totalRecords = 0;

    protected function init()
    {
        parent::init();
        $this->setTemplate('components/paginator.phtml');
        $this->addTagIdentifier('paginator');
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * @param $totalRecords
     * @return $this
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = max(0, $totalRecords);
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * @param int $recordsPerPage
     * @return $this
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = max(0, $recordsPerPage);
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return ceil($this->getTotalRecords() / $this->getRecordsPerPage());
    }

}