<?php

/*
 * Article Categories bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2017, Petzka
 * @author     Petzka <https://petzka.pl>
 * @license    MIT
 */

\Contao\Controller::loadDataContainer('tl_user');
\Contao\System::loadLanguageFile('tl_user');

/*
 * Extend palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('articleCategories_legend', 'article_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField('articlecategories', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('articlecategories_roots', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('articlecategories_default', 'articleCategories_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group');

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['articlecategories'] = &$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories'];
$GLOBALS['TL_DCA']['tl_user_group']['fields']['articlecategories_default'] = &$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories_default'];
$GLOBALS['TL_DCA']['tl_user_group']['fields']['articlecategories_roots'] = &$GLOBALS['TL_DCA']['tl_user']['fields']['articlecategories_roots'];
