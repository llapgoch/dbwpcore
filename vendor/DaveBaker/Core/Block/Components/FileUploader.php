<?php

namespace DaveBaker\Core\Block\Components;
use DaveBaker\Core\Definitions\Api;

/**
 * Class FileUploader
 * @package DaveBaker\Core\Block\Components
 */
class FileUploader
    extends \DaveBaker\Core\Block\Html\Base
{
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
     * @return \DaveBaker\Core\Block\Html\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();
        wp_enqueue_script('dbwpcore_file_uploader');

        $this->addChildBlock([
            $this->createBlock(
                '\DaveBaker\Form\Block\Input\File',
                null,
                'fileuploader'
            )->addClass('js-file-input'),

            $this->createBlock(
                '\DaveBaker\Form\Block\Button',
                null,
                'uploadbutton'
            )->setButtonName('Upload')
                ->addClass('js-upload-button'),

            $this->createBlock(
                '\DaveBaker\Core\Block\Components\ProgressBar',
                null,
                'progressbar'
            )
        ]);
    }

}