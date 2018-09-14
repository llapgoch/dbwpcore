<?php

namespace DaveBaker\Core\Helper;

use \DaveBaker\Core\Definitions\Upload as UploadDefinition;
/**
 * Class Upload
 * @package DaveBaker\Core\Helper
 */
class Upload extends Base
{
    const UPLOAD_DIRECTORY = 'dbwpcore';

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
     * @param null|int $parentId
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUploadCollection(
        $type = UploadDefinition::UPLOAD_TYPE_GENERAL,
        $parentId = null
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

        if($parentId){
            $collection->where('parent_id=?', $parentId);
        }

        return $collection;
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
}