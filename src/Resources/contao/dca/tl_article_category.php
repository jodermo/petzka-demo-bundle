<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

\Contao\System::loadLanguageFile('tl_article_category');

/*
 * Table tl_article_category
 */
$GLOBALS['TL_DCA']['tl_article_category'] = [
    // Config
    'config' => [
        'label' => $GLOBALS['TL_LANG']['tl_article_category']['categories'][0],
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'backlink' => 'do=article',
        'onload_callback' => [
            ['petzka_article_categories.listener.data_container.article_category', 'onLoadCallback'],
        ],
        'onsubmit_callback' => [
           // ['petzka_article_categories.listener.data_container.feed', 'onSubmitCallback'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
                'alias' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 5,
            'icon' => 'bundles/petzkaarticlecategories/icon.png',
            'paste_button_callback' => [
                'petzka_article_categories.listener.data_container.article_category',
                'onPasteButtonCallback',
            ],
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['title', 'frontendTitle'],
            'format' => '%s <span style="padding-left:3px;color:#b3b3b3;">[%s]</span>',
            'label_callback' => ['petzka_article_categories.listener.data_container.article_category', 'onLabelCallback'],
        ],
        'global_operations' => [
            'toggleNodes' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['toggleAll'],
                'href' => 'ptg=all',
                'class' => 'header_toggle',
            ],
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['copy'],
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'copyChilds' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['copyChilds'],
                'href' => 'act=paste&amp;mode=copy&amp;childs=1',
                'icon' => 'copychilds.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'cut' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['cut'],
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['show'],
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
            'toggle' => [
                'label' => &$GLOBALS['TL_LANG']['tl_article_category']['toggle'],
                'attributes' => 'onclick="Backend.getScrollOffset();"',
                'haste_ajax_operation' => [
                    'field' => 'published',
                    'options' => [
                        [
                            'value' => 0,
                            'icon' => 'invisible.svg',
                        ],
                        [
                            'value' => 1,
                            'icon' => 'visible.svg',
                        ],
                    ],
                ],
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,alias,frontendTitle,cssClass;{details_legend:hide},description,image;{modules_legend:hide},hideInList,hideInReader,excludeInRelated;{redirect_legend:hide},jumpTo;{publish_legend},published',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'sorting' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['title'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'default' => ''],
        ],
        'frontendTitle' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['frontendTitle'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
        ],
        'alias' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['alias'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'alias',
                'unique' => true,
                'spaceToUnderscore' => true,
                'maxlength' => 128,
                'tl_class' => 'w50',
            ],
            'save_callback' => [
                ['petzka_article_categories.listener.data_container.article_category', 'onGenerateAlias'],
            ],
            'sql' => ['type' => 'binary', 'length' => 128, 'default' => ''],
        ],
        'cssClass' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['cssClass'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'default' => ''],
        ],
        'description' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['description'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql' => ['type' => 'text', 'notnull' => false],
        ],
        'image' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['image'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => [
                'files' => true,
                'filesOnly' => true,
                'fieldType' => 'radio',
                'extensions' => \Contao\Config::get('validImageTypes'),
                'tl_class' => 'clr',
            ],
            'sql' => ['type' => 'binary', 'length' => 16, 'notnull' => false],
        ],
        'hideInList' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['hideInList'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => 0],
        ],
        'hideInReader' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['hideInReader'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => 0],
        ],
        'excludeInRelated' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['excludeInRelated'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => 0],
        ],
        'jumpTo' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['jumpTo'],
            'exclude' => true,
            'inputType' => 'pageTree',
            'eval' => ['fieldType' => 'radio'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'published' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_category']['published'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => 0],
        ],
    ],
];

/*
 * Enable multilingual features
 */
if (\Petzka\DemoBundle\MultilingualHelper::isActive()) {
    // Config
    $GLOBALS['TL_DCA']['tl_article_category']['config']['dataContainer'] = 'Multilingual';
    $GLOBALS['TL_DCA']['tl_article_category']['config']['langColumn'] = 'language';
    $GLOBALS['TL_DCA']['tl_article_category']['config']['langPid'] = 'lid';
    $GLOBALS['TL_DCA']['tl_article_category']['config']['sql']['keys']['language,lid'] = 'index';

    // Fields
    $GLOBALS['TL_DCA']['tl_article_category']['fields']['language']['sql'] = ['type' => 'string', 'length' => 5, 'default' => ''];
    $GLOBALS['TL_DCA']['tl_article_category']['fields']['lid']['sql'] = ['type' => 'integer', 'unsigned' => true, 'default' => 0];
    $GLOBALS['TL_DCA']['tl_article_category']['fields']['title']['eval']['translatableFor'] = '*';
    $GLOBALS['TL_DCA']['tl_article_category']['fields']['frontendTitle']['eval']['translatableFor'] = '*';
    $GLOBALS['TL_DCA']['tl_article_category']['fields']['description']['eval']['translatableFor'] = '*';

    $GLOBALS['TL_DCA']['tl_article_category']['fields']['alias']['eval']['translatableFor'] = '*';
    unset($GLOBALS['TL_DCA']['tl_article_category']['fields']['alias']['eval']['spaceToUnderscore'], $GLOBALS['TL_DCA']['tl_article_category']['fields']['alias']['eval']['unique']);
}