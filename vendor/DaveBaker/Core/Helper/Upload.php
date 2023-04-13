<?php

namespace DaveBaker\Core\Helper;

use \DaveBaker\Core\Definitions\Upload as UploadDefinition;

/**
 * Class Upload
 * @package DaveBaker\Core\Helper
 */
class Upload extends Base
{
    /** @var array */
    protected $temporaryIds = [];

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Only generate one per page load, use one that's been posted if available
     */
    public function getTemporaryIdForSession(
        $prefix = UploadDefinition::TEMPORARY_PREFIX,
        $postActualType = null,
        $getFromPostIfSubmitted = true
    ) {

        // Get the temporary ID from the post if we're in that context. Match using the actual key to the temporary ID
        /** @var $postTemporaryIds array */
        if ($getFromPostIfSubmitted && ($postTemporaryIds  = $this->getRequest()->getPostParam(UploadDefinition::TEMPORARY_IDENTIFIER_ELEMENT_NAME))) {
            if (!$postActualType) {
                throw new Exception("Post actual type must be provided");
            }

            foreach ($postTemporaryIds as $postKey => $postTemporaryId) {
                if ($postTemporaryId === $postActualType) {
                    return $postKey;
                }
            }

            return $postTemporaryIds;
        }

        if (!isset($this->temporaryIds[$prefix])) {
            $this->temporaryIds[$prefix] = uniqid($prefix);
        }

        return $this->temporaryIds[$prefix];
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return wp_upload_dir()['basedir'];
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return wp_upload_dir()['baseurl'];
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return trailingslashit(untrailingslashit($this->getBaseDir()) . DS . UploadDefinition::UPLOAD_DIRECTORY);
    }

    /**
     * @return string
     */
    public function getUploadUrl()
    {
        return trailingslashit(untrailingslashit($this->getBaseUrl()) . DS . UploadDefinition::UPLOAD_DIRECTORY);
    }

    /**
     * @return $this
     * @param string $type
     * @throws Exception
     */
    public function createUploadDir($type)
    {
        $uploadDir = $this->getUploadDir() . DS . $type;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Could not create upload directory ' . $uploadDir);
            }
        }

        return $this;
    }

    /**
     * @param string $type
     * @param string|int $identifier
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUploadCollection(
        $type = UploadDefinition::UPLOAD_TYPE_GENERAL,
        $identifier = null
    ) {

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection = $this->createAppObject(
            \DaveBaker\Core\Model\Db\Core\Upload\Collection::class
        )->where('is_deleted=?', 0)
            ->where('upload_type=?', $type)
            ->order('created_at DESC');

        $collection->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{file_upload}}.created_by_id",
            ['created_by_name' => 'user_login']
        );

        if ($identifier) {
            if ($type == UploadDefinition::UPLOAD_TYPE_TEMPORARY) {
                $collection->where('temporary_id=?', $identifier);
            } else {
                $collection->where('parent_id=?', $identifier);
            }
        }

        return $collection;
    }

    /**
     * @param $identifier
     * @param $uploadType
     * @param $parentId
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function assignTemporaryUploadsToParent($identifier, $uploadType, $parentId)
    {
        $items = $this->getUploadCollection(
            UploadDefinition::UPLOAD_TYPE_TEMPORARY,
            $identifier
        )->load();

        foreach ($items as $item) {
            $item->setTemporaryId(null)
                ->setParentId($parentId)
                ->setUploadType($uploadType)->save();
        }

        return $this;
    }

    /**
     * @param \DaveBaker\Core\Model\Db\Core\Upload $upload
     * @return string
     *
     * Use the db model's getUrl rather than this method, this simply builds a url path
     * The getUrl method in the model will check whether the file has a parent, and return the correct path
     */
    public function makeUploadUrl(
        \DaveBaker\Core\Model\Db\Core\Upload $upload
    ) {
        
        if($upload->isModeOriginal()) {
            return $this->getUploadUrl() . $upload->getId() . "." . $upload->getExtension();
        }

        // V2
        return $this->getUploadUrl() . $upload->getUploadType() . "/" . $this->getUploadFilename($upload);
    }

    /**
     *
     * @param \DaveBaker\Core\Model\Db\Core\Upload $upload
     * @return string
     */
    public function getUploadFilename(
        \DaveBaker\Core\Model\Db\Core\Upload $upload
    ) {
        return $upload->getFileHash() . "_" . $upload->getId() . "." . $upload->getExtension();
    }

    /**
     * @param \DaveBaker\Core\Model\Db\Core\Upload $upload
     * @return string
     *
     * Use the db model's get rather than this method, this simply builds a url path
     * The getUrl method in the model will check whether the file has a parent, and return the correct path
     */
    public function makeUploadPath(
        \DaveBaker\Core\Model\Db\Core\Upload $upload
    ) {
        if($upload->isModeOriginal()) {
            return $this->getUploadDir() . $upload->getId() . "." . $upload->getExtension();
        }

        // V2 
        return $this->getUploadDir() . $upload->getUploadType() . DS . $this->getUploadFilename($upload);
    }
}
