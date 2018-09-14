<?php
namespace DaveBaker\Core\Api\Core;
use DaveBaker\Core\Api\Exception;

/**
 * Class Core
 * @package DaveBaker\Core\Api
 */
class File
    extends \DaveBaker\Core\Api\Base
    implements \DaveBaker\Core\Api\ControllerInterface
{
    const ALLOWED_MIME_TYPES_CONFIG_KEY = 'uploadAllowedMimeTypes';
    /** @var array  */
    protected $allowedMimeTypes = [];

    /**
     * @param $params
     * @throws Exception
     */
    public function uploadAction($params)
    {
        if(!$_FILES || !count($_FILES)){
            throw new Exception('No files provided');
        }

        array_map([$this, 'validateFile'], $_FILES);

    }

    /**
     * @param $file
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function validateFile($file)
    {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('The file was too large');
            default:
                throw new Exception('An error occurred');
        }


        // Check mime types
        if(!($mimeTypes = $this->getAllowedMimeTypes())){
            throw new Exception('Allowed mime types not set');
        }

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        if(!in_array($fileInfo->file($file['tmp_name']), $mimeTypes)){
            throw new Exception('Filetype not allowed');
        }

    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getAllowedMimeTypes()
    {
        if(!$this->allowedMimeTypes){
            $this->allowedMimeTypes = array_map('trim', explode(
                ',', $this->getApp()->getGeneralConfig()->getConfigValue(
                self::ALLOWED_MIME_TYPES_CONFIG_KEY
            )));
        }

        return $this->allowedMimeTypes;
    }
}
