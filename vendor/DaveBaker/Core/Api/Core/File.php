<?php
namespace DaveBaker\Core\Api\Core;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Definitions\Upload as UploadDefinition;

/**
 * Class File
 * @package DaveBaker\Core\Api
 */
class File
    extends \DaveBaker\Core\Api\Base
    implements \DaveBaker\Core\Api\ControllerInterface
{
    const ALLOWED_MIME_TYPES_CONFIG_KEY = 'uploadAllowedMimeTypes';

    /** @var array */
    protected $allowedMimeTypes = [];
    /** @var string */
    protected $uploadType;
    /** @var int */
    protected $parentId;
    /** @var string  */
    protected $namespaceCode = 'file_upload_api';

    /**
     * @return string
     */
    public function getUploadType()
    {
        return $this->uploadType;
    }

    /**
     * @param string $uploadType
     * @return $this
     */
    public function setUploadType($uploadType)
    {
        $this->uploadType = $uploadType;
        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function uploadAction($params)
    {
        if(!$_FILES || !count($_FILES)){
            throw new Exception('No files provided');
        }

        if(!isset($params['parent_id']) || empty($params['parent_id'])){
            $params['parent_id'] = uniqid();
        }

        if(!isset($params['upload_type'])){
            $params['upload_type'] = UploadDefinition::UPLOAD_TYPE_GENERAL;
        }

        $this->uploadType = $params['upload_type'];
        $this->parentId = $params['parent_id'];

        $results = [];
        // Do all validation before performing uploads (deny all if any fail)
        @array_map([$this, 'validateFile'], $_FILES);

        $results[] = @array_map([$this, 'performUpload'], $_FILES);

        $context = $this->fireEvent('upload_complete', [
            'results' => $results,
            'params' => $params
        ]);

        return $context->getResults();
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
     * @param array $file
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function performUpload($file = [])
    {
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
            ->setUploadType($this->uploadType)
            ->setParentId($this->parentId);

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
                $this->getUploadHelper()->getUploadDir() .
                    $fileInstance->getId() . "." . $pathInfo['extension']
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
     * @throws \DaveBaker\Core\Object\Exception
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
            throw new Exception('File type not allowed');
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
