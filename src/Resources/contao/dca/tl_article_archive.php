<?php

/*
 * Article Categories bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2017, Petzka
 * @author     Petzka <https://petzka.com>
 * @license    MIT
 */

$GLOBALS['TL_DCA']['tl_article_archive']['config']['onload_callback'][] = [
    'petzka_article_categories.listener.data_container.article_archive',
    'onLoadCallback',
];

/*
 * Replace the feed generation callback
 */
if (false !== ($index = \array_search(['tl_article_archive', 'generateFeed'], $GLOBALS['TL_DCA']['tl_article_archive']['config']['onload_callback'], true))) {
    $GLOBALS['TL_DCA']['tl_article_archive']['config']['onload_callback'][$index] = ['petzka_article_categories.listener.data_container.feed', 'onLoadCallback'];
}

/*
 * Add global operations
 */
array_insert(
    $GLOBALS['TL_DCA']['tl_article_archive']['list']['global_operations'], 1, [
        'categories' => [
            'label' => &$GLOBALS['TL_LANG']['tl_article_archive']['categories'],
            'href' => 'table=tl_article_category',
            'icon' => 'bundles/petzkaarticlecategories/icon.png',
            'attributes' => 'onclick="Backend.getScrollOffset()"',
        ],
    ]
);

/*
 * Extend palettes
 */
$GLOBALS['TL_DCA']['tl_article_archive']['palettes']['__selector__'][] = 'limitCategories';
$GLOBALS['TL_DCA']['tl_article_archive']['subpalettes']['limitCategories'] = 'categories';

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('categories_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('limitCategories', 'categories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_article_archive');

/*
 * Add fields to tl_article_archive
 */
$GLOBALS['TL_DCA']['tl_article_archive']['fields']['limitCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_article_archive']['limitCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_article_archive']['fields']['categories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_article_archive']['categories'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'options_callback' => ['petzka_article_categories.listener.data_container.article', 'onCategoriesOptionsCallback'],
    'eval' => ['mandatory' => true, 'multiple' => true, 'fieldType' => 'checkbox'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];
