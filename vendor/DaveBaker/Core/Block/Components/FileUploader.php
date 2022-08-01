<?php

namespace DaveBaker\Core\Block\Components;

use DaveBaker\Core\Definitions\Api;
use DaveBaker\Core\Definitions\Upload;
use Exception;

/**
 * Class FileUploader
 * @package DaveBaker\Core\Block\Components
 */
class FileUploader
extends \DaveBaker\Core\Block\Html\Base
{
    /** @var string */
    protected $actualType;
    /** @var string */
    protected $identifier;

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('file-uploader-component');
        $this->addJsDataItems(
            ['endpoint' => $this->getUrlHelper()->getApiUrl(Api::ENDPOINT_FILE_UPLOAD)]
        );
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('components/file-uploader.phtml');
    }

    /**
     * Get the type this should be set to when copying from temp
     *
     * @return string
     */
    public function getActualType()
    {
        return $this->actualType;
    }

    /**
     *
     * @param string $actualType
     * @return self
     */
    public function setActualType($actualType)
    {
        $this->actualType = $actualType;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();
        wp_enqueue_script('dbwpcore_file_uploader');

        $id = $this->getUtilHelper()->createUrlKeyFromText($this->getName() . "_element", '_');

        if (!$this->getActualType() || !$this->getIdentifier()) {
            throw new Exception("Actual type or identifier not set in uploader");
        }

        $this->addChildBlock([
            $this->createBlock(
                '\DaveBaker\Form\Block\Label',
                null,
                'label'
            )->addClass('upload-component-label')
                ->setLabelName('Upload Attachment')
                ->setForId($id)
                ->addTagIdentifier('file-uploader-component-label')
                ->addClass('js-file-label upload-component-label'),

            $this->createBlock(
                '\DaveBaker\Form\Block\Input\File',
                null,
                'fileuploader'
            )->addClass('js-file-input upload-component-input')
                ->addAttribute(['multiple' => 'multiple', 'id' => $id]),

            $this->createBlock(
                '\DaveBaker\Form\Block\Input\Hidden',
                null,
                'temporaryIdentifier'
            )->addClass('js-file-ids')
                ->setElementName(Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME . '[' . $this->getIdentifier() . ']')
                ->setElementValue($this->getActualType()),


            $this->createBlock(
                '\DaveBaker\Core\Block\Components\ProgressBar',
                null,
                'progressbar'
            )->addClass('upload-component-progress')
        ]);
    }
}
