<?php
namespace DaveBaker\Core\Block\Components;

/**
 * Class UploadedFileList
 * @package DaveBaker\Core\Block\Components
 */
class UploadedFileList
    extends \DaveBaker\Core\Block\Html\Base
{
    /** @var array  */
    protected $files = [];

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->addTagIdentifier('uploaded-file-list');
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('components/uploaded-file-list.phtml');
    }

    /**
     * @param array $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}