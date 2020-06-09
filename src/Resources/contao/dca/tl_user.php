<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('articleCategories_legend', 'article_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('articlecategories', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('articlecategories_roots', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('articlecategories_default', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user');

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['articlecategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options' => ['manage'],
    'reference' => &$GLOBALS['TL_LANG']['tl_user']['articlecategoriesRef'],
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories_roots'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['articlecategories_roots'],
    'exclude' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories_default'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['articlecategories_default'],
    'exclude' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];
