<?php
namespace DaveBaker\Core\Api\Core\File;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Definitions\Upload as UploadDefinition;

use DaveBaker\Core\Definitions\Roles;

/**
 * Class File
 * @package DaveBaker\Core\Api
 */
class Upload
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
