<?php

namespace DaveBaker\Core\Definitions;
/**
 * Class Upload
 * @package DaveBaker\Core\Definitions
 */
class Upload
{
    const UPLOAD_TYPE_GENERAL = 'general';
    // Temporary is used before the upload has been assigned to the correct entity, E.g. when uploading files
    // for something which doesn't yet have an ID for the file's parent_id
    const UPLOAD_TYPE_TEMPORARY = 'temporary';
    const UPLOAD_DIRECTORY = 'dbwpcore';
    const TEMPORARY_IDENTIFIER_ELEMENT_NAME = 'temporary_identifier';
    const TEMPORARY_PREFIX = 'upload_tmp_';

    const MODE_ORIGINAL = 'original';
    const MODE_V2 = 'v2';
}