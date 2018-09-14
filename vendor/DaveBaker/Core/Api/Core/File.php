<?php
namespace DaveBaker\Core\Api\Core;
use DaveBaker\Core\Api\Exception;

/**
 * Class File
 * @package DaveBaker\Core\Api
 */
class File
    extends \DaveBaker\Core\Api\Base
    implements \DaveBaker\Core\Api\ControllerInterface
{
    const ALLOWED_MIME_TYPES_CONFIG_KEY = 'uploadAllowedMimeTypes';
    const UPLOAD_TYPE_GENERAL = 'general';

    /** @var array */
    protected $allowedMimeTypes = [];
    /** @var string */
    protected $uploadType;

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function uploadAction($params)
    {
        if(!$_FILES || !count($_FILES)){
            throw new Exception('No files provided');
        }

        $this->uploadType = isset($params['upload_type']) ? $params['upload_type'] : self::UPLOAD_TYPE_GENERAL;

        $results = [];
        $results[] = @array_map([$this, 'performUpload'], $_FILES);

        return $results;
    }

    /**
     * @return \DaveBaker\Core\Model\Db\Core\Upload\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getFileCollection()
    {
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload\Collection');
    }

    /**
     * @param $file
     * @return array
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function performUpload($file)
    {
        $this->validateFile($file);

        $this->getUploadHelper()->createUploadDir();
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file['tmp_name']);

        $hashedFile = hash_file('md5', $file['tmp_name']);
        $existingCollection = $this->getFileCollection()
            ->where('file_hash=?', $hashedFile)
            ->where('file_parent_id IS NULL');

        $existingFile = $existingCollection->firstItem();

        $pathInfo = pathinfo($file['name']);
        // Use the already uploaded file
        $fileInstance = $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload')
            ->setFileHash($hashedFile)
            ->setFilename($pathInfo['basename'])
            ->setExtension($pathInfo['extension'])
            ->setMimeType($mimeType)
            ->setUploadType($this->uploadType);

        if($this->getUserHelper()->isLoggedIn()){
            $fileInstance->setLastUpdatedById($this->getUserHelper()->getCurrentUserId());
            $fileInstance->setCreatedById($this->getUserHelper()->getCurrentUserId());
        }


        if($existingFile){
            $fileInstance->setFileParentId($existingFile->getId())->save();
        } else {
            $fileInstance->save();

            move_uploaded_file(
                $file['tmp_name'],
                $this->getUploadHelper()->getUploadDir() . $fileInstance->getId() . "." . $pathInfo['extension']
            );
        }

        return [
            'file_id' => $fileInstance->getId(),
            'url' => $fileInstance->getUrl()
        ];
    }

    /**
     * @param $file
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function validateFile($file)
    {
        if(isset($file['error'])) {
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
        }

        // Check mime types
        if(!($mimeTypes = $this->getAllowedMimeTypes())){
            throw new Exception('Allowed mime types not set');
        }

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file['tmp_name']);

        if(!in_array($mimeType, $mimeTypes)){
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
