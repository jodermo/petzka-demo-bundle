<?php
/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

\Contao\Controller::loadDataContainer('tl_module');
\Contao\System::loadLanguageFile('tl_module');

/*
 * Replace the feed generation callback
 */
if ('article' === \Contao\Input::get('do')
    && false !== ($index = \array_search(['tl_content_article', 'generateFeed'], $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'], true))
) {
    $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][$index] = ['petzka_article_categories.listener.data_container.feed', 'onLoadCallback'];
}

/*
 * Add palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['articlefilter'] = '{type_legend},type;{include_legend},article_module,article_filterCategories,article_relatedCategories,article_filterDefault,article_filterPreserve;{link_legend:hide},article_categoryFilterPage;{image_legend:hide},article_categoryImgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

/*
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['article_filterCategories'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterCategories'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_relatedCategories'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_relatedCategories'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_includeSubcategories'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_includeSubcategories'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_filterDefault'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterDefault'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_filterPreserve'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_filterPreserve'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_categoryFilterPage'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryFilterPage'];
$GLOBALS['TL_DCA']['tl_content']['fields']['article_categoryImgSize'] = &$GLOBALS['TL_DCA']['tl_module']['fields']['article_categoryImgSize'];

$GLOBALS['TL_DCA']['tl_content']['fields']['article_module'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['module'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['petzka_article_categories.listener.data_container.content', 'onGetArticleModules'],
    'eval' => ['mandatory' => true, 'chosen' => true, 'submitOnChange' => true],
    'wizard' => [['tl_content', 'editModule']],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];
