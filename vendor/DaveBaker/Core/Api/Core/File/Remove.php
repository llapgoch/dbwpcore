<?php
namespace DaveBaker\Core\Api\Core\File;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Definitions\Upload as UploadDefinition;

use DaveBaker\Core\Definitions\Roles;

/**
 * Class File
 * @package DaveBaker\Core\Api
 */
class Remove
    extends \DaveBaker\Core\Api\Base
    implements \DaveBaker\Core\Api\ControllerInterface
{
    /** @var string  */
    protected $namespaceCode = 'file_upload_api_remove';
    /** @var array */
    protected $capabilities = [
        Roles::CAP_UPLOAD_FILE_REMOVE, 
        Roles::CAP_UPLOAD_FILE_REMOVE_ANY
    ];

    public function executeAction($params)
    {
        // Check there's an ID present 
        $this->validateRequiredParameters(
            ['id'],
            $params
        );

        /** @var \DaveBaker\Core\Model\Db\Core\Upload $model */
        $model = $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload')->load($params['id']);

        if(!$model->getId() || $model->getIsDeleted()){
            throw new Exception("File could not be found");
        }
        
        $currentUser = $this->getUserHelper()->getCurrentUser();

        if(($currentUser->getId() !== $model->getCreatedById()) 
            && $this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_REMOVE_ANY) == false){
                throw new Exception('Permission denied');
        }

        // Don't unlink files anymore, multiple entries can now share the same hash dependent on deleted flags or upload_type=temporary
        // Just update the entry to have the deleted flag
        $model->setIsDeleted(1)->save();

        // Check whether any other items are using this file via the hash
        // $existingCollection = $this->getFileCollection()
        //   ->where('file_hash=?', $model->getFileHash())
        //   ->where('is_deleted=?', 0)
        //   ->where('id<>?', $model->getId());

        // $existingItems = $existingCollection->load();
        

        // If no other entries are using the file, unlink it
        // if(count($existingItems) == 0){
        //     if(file_exists($model->getFilePath())){
        //         unlink($model->getFilePath());
        //     }
        // }

        return true;
    }

    /**
     * @return \DaveBaker\Core\Model\Db\Core\Upload\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getFileCollection()
    {
        return $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload\Collection');
    }

}
