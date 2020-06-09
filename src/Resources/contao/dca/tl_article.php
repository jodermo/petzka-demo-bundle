<?php
/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

/*
if (false !== ($index = \array_search(['tl_article', 'generateFeed'], $GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'], true))) {
    $GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][$index] = ['petzka_article_categories.listener.data_container.feed', 'onLoadCallback'];
}
*/

/*
 * Add global callbacks
 */
$GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][] = ['petzka_article_categories.listener.data_container.article', 'onLoadCallback'];
$GLOBALS['TL_DCA']['tl_article']['config']['onsubmit_callback'][] = ['petzka_article_categories.listener.data_container.article', 'onSubmitCallback'];

/*
 * Extend palettes
 */
$paletteManipulator = \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('category_legend', 'title_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('categories', 'category_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
;

foreach ($GLOBALS['TL_DCA']['tl_article']['palettes'] as $name => $palette) {
    if (is_string($palette)) {
        $paletteManipulator->applyToPalette($name, 'tl_article');
    }
}

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_article']['fields']['categories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_article']['categories'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'options_callback' => ['petzka_article_categories.listener.data_container.article', 'onCategoriesOptionsCallback'],
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox'],
    'relation' => [
        'type' => 'haste-ManyToMany',
        'load' => 'lazy',
        'table' => 'tl_article_category',
        'referenceColumn' => 'article_id',
        'fieldColumn' => 'category_id',
        'relationTable' => 'tl_article_categories',
    ],
];

