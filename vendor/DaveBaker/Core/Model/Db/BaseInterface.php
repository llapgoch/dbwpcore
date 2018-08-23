<?php

namespace DaveBaker\Core\Model\Db;

interface BaseInterface
{
    public function load($id, $column = '');
    public function save();
    public function delete();
    public function getTableName();
    public function getId();
    

}