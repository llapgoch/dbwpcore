<?php

namespace DaveBaker\Core\Definitions;
/**
 * Class General
 * @package DaveBaker\Core\Definitions
 */
class Roles
{
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_EDITOR = 'editor';
    const ROLE_AUTHOR = 'author';
    const ROLE_CONTRIBUTOR = 'contributor';

    const CAP_UPLOAD_FILE_ADD = 'upload_file_add';
    const CAP_UPLOAD_FILE_REMOVE = 'upload_file_remove';
    /** Capability allows user to remove any file, not just ones they've uploaded */
    const CAP_UPLOAD_FILE_REMOVE_ANY = 'upload_file_remove_any';
}