<?php

namespace DaveBaker\Core\Model\Db\Core;
use DaveBaker\Core\Model\Db\Exception;

/**
 * Class File
 * @package DaveBaker\Core\Model\Db
 */
class Upload extends \DaveBaker\Core\Model\Db\Base
{
    protected function init()
    {
        $this->tableName = 'file_upload';
        $this->idColumn = 'id';
    }

    public function getUrl()
    {
        if($parent = $this->getFileParentId()){
            $parent = $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload')
                ->load($this->getFileParentId());

            if(!$parent->getId()){
                throw new Exception('The file parent does not exist');
            }

            return $this->getUploadHelper()->makeUploadUrl($parent);
        }

        return $this->getUploadHelper()->makeUploadUrl($this);
    }
}