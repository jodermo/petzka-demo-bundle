<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


$GLOBALS['FE_MOD']['miscellaneous']['demoModule'] = 'Petzka\DemoBundle\Module\PetzkaDemoModule';

$GLOBALS['TL_CTE']['miscellaneous']['demoElement'] = 'Petzka\DemoBundle\ContentElement\PetzkaDemoContentElement';

$GLOBALS['BE_MOD']['content']['article']['tables'][] = 'tl_article_category';

/*
 * Back end form fields
 */
$GLOBALS['BE_FFL']['articleCategoriesPicker'] = 'Petzka\DemoBundle\Widget\ArticleCategoriesPickerWidget';

/*
 * Front end modules
 */
//$GLOBALS['FE_MOD']['article']['articlearchive'] = 'Petzka\DemoBundle\FrontendModule\ArticleArchiveModule';
//$GLOBALS['FE_MOD']['article']['articlecategories'] = '\Petzka\DemoBundle\FrontendModule\ArticleCategoriesModule';
$GLOBALS['FE_MOD']['article']['articlecategories_cumulative'] = '\Petzka\DemoBundle\FrontendModule\CumulativeFilterModule';
//$GLOBALS['FE_MOD']['article']['articlelist'] = '\Petzka\DemoBundle\FrontendModule\ArticleListModule';
// $GLOBALS['FE_MOD']['article']['articlemenu'] = '\Petzka\DemoBundle\FrontendModule\ArticleMenuModule';

/*
 * Content elements
 */
// $GLOBALS['TL_CTE']['includes']['articlefilter'] = '\Petzka\DemoBundle\ContentElement\ArticleCFilterElement';

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_article_category'] = '\Petzka\DemoBundle\Model\ArticleCategoryModel';

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = [
    'petzka_article_categories.listener.change_language',
    'onChangeLanguageNavigation',
];
$GLOBALS['TL_HOOKS']['executePostActions'][] = ['petzka_article_categories.listener.ajax', 'onExecutePostActions'];
$GLOBALS['TL_HOOKS']['articleListCountItems'][] = ['petzka_article_categories.listener.article', 'onArticleListCountItems'];
$GLOBALS['TL_HOOKS']['articleListFetchItems'][] = ['petzka_article_categories.listener.article', 'onArticleListFetchItems'];
$GLOBALS['TL_HOOKS']['parseArticles'][] = ['petzka_article_categories.listener.template', 'onParseArticles'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['petzka_article_categories.listener.insert_tags', 'onReplace'];


/*
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'articlecategories';
$GLOBALS['TL_PERMISSIONS'][] = 'articlecategories_default';
$GLOBALS['TL_PERMISSIONS'][] = 'articlecategories_roots';
