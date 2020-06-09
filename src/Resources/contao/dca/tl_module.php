<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['demoModule'] = '{title_legend},name,headline,type;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'article_customCategories';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'article_relatedCategories';
$GLOBALS['TL_DCA']['tl_module']['palettes']['articlecategories'] = '{title_legend},name,headline,type;{config_legend},article_archives,article_showQuantity,article_resetCategories,article_showEmptyCategories,article_enableCanonicalUrls,article_includeSubcategories,showLevel;{reference_legend:hide},article_categoriesRoot,article_customCategories;{redirect_legend:hide},article_forceCategoryUrl,jumpTo;{template_legend:hide},navigationTpl,customTpl;{image_legend:hide},article_categoryImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['articlecategories_cumulative'] = '{title_legend},name,headline,type;{config_legend},article_archives,article_showQuantity,article_resetCategories,article_enableCanonicalUrls,article_includeSubcategories,article_filterCategoriesUnion;{reference_legend:hide},article_categoriesRoot,article_customCategories;{redirect_legend:hide},jumpTo;{template_legend:hide},navigationTpl,customTpl;{image_legend:hide},article_categoryImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['article_customCategories'] = 'article_categories';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['article_relatedCategories'] = 'article_relatedCategoriesOrder,article_categoriesRoot';

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('redirect_legend', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('article_filterCategories', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterCategoriesCumulative', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterCategoriesUnion', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_relatedCategories', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_includeSubcategories', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterDefault', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterPreserve', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_categoryFilterPage', 'redirect_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_categoryImgSize', 'image_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('articlelist', 'tl_module');

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField('article_filterCategories', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterCategoriesCumulative', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterCategoriesUnion', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_includeSubcategories', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterDefault', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_filterPreserve', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_categoryImgSize', 'image_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('articlearchive', 'tl_module')
    ->applyToPalette('articlemenu', 'tl_module');

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('redirect_legend', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('article_categoryFilterPage', 'redirect_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('articlearchive', 'tl_module');

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('redirect_legend', 'config_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('article_categoryFilterPage', 'redirect_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('article_categoryImgSize', 'image_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('articlereader', 'tl_module');

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['article_categories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_categories'],
    'exclude' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_customCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_customCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_filterCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterCategoriesCumulative'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_filterCategoriesCumulative'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_relatedCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_relatedCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_relatedCategoriesOrder'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_relatedCategoriesOrder'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['default', 'best_match'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['article_relatedCategoriesOrderRef'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_includeSubcategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_includeSubcategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterCategoriesUnion'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_filterCategoriesUnion'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_enableCanonicalUrls'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_enableCanonicalUrls'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterDefault'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_filterDefault'],
    'exclude' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'eval' => ['multiple' => true, 'fieldType' => 'checkbox', 'tl_class' => 'clr'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterPreserve'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_filterPreserve'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_resetCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_resetCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_showEmptyCategories'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_showEmptyCategories'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_forceCategoryUrl'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_forceCategoryUrl'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoriesRoot'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_categoriesRoot'],
    'exclude' => true,
    'inputType' => 'articleCategoriesPicker',
    'foreignKey' => 'tl_article_category.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryFilterPage'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['article_categoryFilterPage'],
    'exclude' => true,
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryImgSize'] = $GLOBALS['TL_DCA']['tl_module']['fields']['imgSize'];
unset($GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryImgSize']['label']);
$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryImgSize']['label'] = &$GLOBALS['TL_LANG']['tl_module']['article_categoryImgSize'];
