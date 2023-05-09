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
        return $this->getUploadHelper()->makeUploadUrl($this);
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
        return $this->getUploadHelper()->makeUploadPath($this);
    }

    /**
     * Deprecated since moving to v2. Upload parents are no longer used
     * @return null
     */
    public function getParentModel()
    {
        return null;
    }
}
