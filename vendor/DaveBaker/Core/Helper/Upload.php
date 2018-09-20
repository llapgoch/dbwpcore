<?php

namespace DaveBaker\Core\Helper;

use \DaveBaker\Core\Definitions\Upload as UploadDefinition;
/**
 * Class Upload
 * @package DaveBaker\Core\Helper
 */
class Upload extends Base
{
    /** @var string */
    protected $temporaryId;

    /**
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Only generate one per page load, use one that's been posted if available
     */
    public function getTemporaryIdForSession()
    {
        if($temporaryId  = $this->getRequest()->getPostParam(UploadDefinition::TEMPORARY_IDENTIFIER_ELEMENT_NAME)){
            return $temporaryId;
        }

        if(!$this->temporaryId){
            $this->temporaryId = uniqid(UploadDefinition::TEMPORARY_PREFIX);
        }

        return $this->temporaryId;
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
     * @throws Exception
     */
    public function createUploadDir()
    {
        if(!file_exists($this->getUploadDir())){
            if(!mkdir($this->getUploadDir())){
                throw new Exception('Could not create upload directory');
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
        $identifier
    ) {

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection = $this->createAppObject(
            '\DaveBaker\Core\Model\Db\Core\Upload\Collection'
        )->where('is_deleted=?', 0)
            ->where('upload_type=?', $type)
            ->order('created_at DESC');

        $collection->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{file_upload}}.created_by_id",
            ['created_by_name' => 'user_login']
        );

        if($identifier){
            if($type == UploadDefinition::UPLOAD_TYPE_TEMPORARY){
                $collection->where('temporary_id=?', $identifier);
            }else {
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

        foreach($items as $item){
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
        return $this->getUploadUrl() . $upload->getId() . "." . $upload->getExtension();
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
        return $this->getUploadDir() . $upload->getId() . "." . $upload->getExtension();
    }
}