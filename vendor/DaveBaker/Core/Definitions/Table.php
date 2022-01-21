<?php

namespace DaveBaker\Core\Definitions;
/**
 * Class Table
 * @package DaveBaker\Core\Definitions
 */
class Table
{
    const CONFIG_SORTABLE_TABLE_CLASS = 'sortableTableClass';
    const CONFIG_SORTABLE_TH_CLASS = 'sortableThClass';
    const CONFIG_SORTABLE_TH_ASC_CLASS = 'sortableThAscClass';
    const CONFIG_SORTABLE_TH_DESC_CLASS = 'sortableThDescClass';
    const CONFIG_SORTABLE_TH_ALPHA_CLASS = 'sortableThAlphaClass';
    const CONFIG_SORTABLE_TH_NUMERIC_CLASS = 'sortableThNumericClass';
    const CONFIG_TABLE_UPDATER_JS_CLASS = 'sortableTableJsClass';
    const CONFIG_SORTABLE_TH_JS_CLASS = 'sortableThJsClass';

    const ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT = 'endpoint';
    const ELEMENT_DATA_KEY_COLUMN_ID = 'data-column-id';

    const HEADER_SORTABLE_ALPHA = 'alpha';
    const HEADER_SORTABLE_DESC = 'desc';
    const HEADER_SORTABLE_ASC = 'asc';
    const HEADER_SORTABLE_NUMERIC = 'numeric';
}