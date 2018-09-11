<?php

namespace DaveBaker\Core\Config;
/**
 * Class Element
 * @package DaveBaker\Core\Config
 */
class Element
    extends Base
    implements ConfigInterface
{
    /**
     * @var array
     *
     * Override and provide theme defaults. Add tag identifiers as necessary
     * These do not have to relate to tag names, identifiers are arbitrary
     */
    protected $config = [
        'elementClasses' => [
            'form-group' => '',
            'heading' => '',
            'messages' => '',
            'table' => '',
            'th' => '',
            'tr' => '',
            'td' => '',
            'a' => '',
            'p' => '',
            'button-anchor' => '',
            'paginator' => 'pagination pagination-xl nomargin pagination-custom'
        ],
        'elementAttributes' => [
            'textarea' => []
        ],
        'generalDisabledClass' => 'disabled',
        'generalActiveClass' => 'active',
        'sortableThClass' => 'sortable',
        'sortableThAscClass' => 'sort-asc',
        'sortableThDescClass' => 'sort-desc',
        'sortableTableClass' => 'table-sortable',
        'sortableThAlphaClass' => 'sort-alpha',
        'sortableTableJsClass' => 'js-table-updater',
        'sortableThJsClass' => 'js-is-sortable'
    ];
}