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

    protected function _construct()
    {
        $this->addTagIdentifier('paginator');
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        if($this->getTotalRecords() == 0){
            $this->addClass($this->getConfig()->getConfigValue('hiddenClass'));
        }
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('components/paginator.phtml');
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPreviousButtonClass()
    {
        return $this->getPage() == 1 ? $this->getConfig()->getConfigValue('generalDisabledClass') : "";
    }

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getNextButtonClass()
    {
        return $this->getPage() == $this->getTotalPages() ? $this->getConfig()->getConfigValue('generalDisabledClass') : "";
    }

    /**
     * @param $pageNumber
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLiItemClass($pageNumber)
    {
        return $this->getPage() == $pageNumber ? $this->getConfig()->getConfigValue('generalActiveClass') : "";
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = max(1, min((int)$page, $this->getTotalPages()));
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return ($this->getPage() - 1) * $this->getRecordsPerPage();
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

    public function getItemHref($i)
    {
        return "#";
    }

    public function getNextButtonHref()
    {
        return "#";
    }

    public function getPreviousButtonHref()
    {
        return "#";
    }

}