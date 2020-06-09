<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

$palette = \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('articleCategories_legend', 'global_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER, true)
    ->addField('articleCategories_param', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page');

if (isset($GLOBALS['TL_DCA']['tl_page']['palettes']['rootfallback'])) {
    $palette->applyToPalette('rootfallback', 'tl_page');
}

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['articleCategories_param'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_page']['articleCategories_param'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'rgxp' => 'alias', 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
];
