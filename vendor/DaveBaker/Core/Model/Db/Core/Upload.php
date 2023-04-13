<?php

namespace DaveBaker\Core\Model\Db\Core;

use DaveBaker\Core\Definitions\Upload as DefinitionsUpload;
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

    /**
     * @return string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrl()
    {
        if ($parent = $this->getParentModel()) {
            return $this->getUploadHelper()->makeUploadUrl($parent);
        }

        return $this->getUploadHelper()->makeUploadUrl(($this));
    }

    /**
     * @return bool
     */
    public function isModeOriginal()
    {
        return $this->getMode() === DefinitionsUpload::MODE_ORIGINAL;
    }

    /**
     * @return bool
     */
    public function isModeV2()
    {
        return $this->getMode() === DefinitionsUpload::MODE_V2;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getFilePath()
    {
        if ($parent = $this->getParentModel()) {
            return $this->getUploadHelper()->makeUploadPath($parent);
        }

        return $this->getUploadHelper()->makeUploadPath($this);
    }

    /**
     * @return null
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getParentModel()
    {
        if ($parent = $this->getFileParentId()) {
            $parent = $this->createAppObject('\DaveBaker\Core\Model\Db\Core\Upload')
                ->load($this->getFileParentId());

            if (!$parent->getId()) {
                throw new Exception('The file parent does not exist');
            }

            return $parent;
        }

        return null;
    }
}
