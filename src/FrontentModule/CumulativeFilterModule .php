<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\FrontendModule;

use Petzka\DemoBundle\Criteria\ArticleCriteria;
use Petzka\DemoBundle\Exception\NoArticlesException;
use Petzka\DemoBundle\Model\ArticleCategoryModel;
use Petzka\DemoBundle\ArticleCategoriesManager;
use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Database;
use Contao\FrontendTemplate;
use Contao\Model\Collection;
use Contao\ModuleArticles;
use Contao\ArticleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Haste\Generator\RowClass;
use Haste\Input\Input;
use Haste\Model\Model;
use Patchwork\Utf8;

class CumulativeFilterModule extends ModuleArticles
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_articlecategories_cumulative';

    /**
     * Active categories.
     *
     * @var Collection|null
     */
    protected $activeCategories;

    /**
     * Article categories of the current article item.
     *
     * @var array
     */
    protected $currentArticleCategories = [];

    /**
     * @var ArticleCategoriesManager
     */
    protected $manager;

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $template = new BackendTemplate('be_wildcard');

            $template->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]).' ###';
            $template->title = $this->headline;
            $template->id = $this->id;
            $template->link = $this->name;
            $template->href = 'contao/?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $template->parse();
        }

        $this->article_archives = $this->sortOutProtected(StringUtil::deserialize($this->article_archives, true));

        // Return if there are no archives
        if (0 === \count($this->article_archives)) {
            return '';
        }

        $this->manager = System::getContainer()->get('codefog_article_categories.manager');
        $this->currentArticleCategories = $this->getCurrentArticleCategories();

        return parent::generate();
    }

    /**
     * Get the URL category separator character
     *
     * @return string
     */
    public static function getCategorySeparator()
    {
        return '__';
    }

    /**
     * Generate the module.
     */
    protected function compile()
    {
        $rootCategoryId = (int) $this->article_categoriesRoot;

        // Set the custom categories either by root ID or by manual selection
        if ($this->article_customCategories) {
            $customCategories = StringUtil::deserialize($this->article_categories, true);
        } else {
            $subcategories = ArticleCategoryModel::findPublishedByPid($rootCategoryId);
            $customCategories = ($subcategories !== null) ? $subcategories->fetchEach('id') : [];
        }

        // Get the subcategories of custom categories
        if (\count($customCategories) > 0 && $this->article_includeSubcategories) {
            $customCategories = ArticleCategoryModel::getAllSubcategoriesIds($customCategories);
        }

        // First, fetch the active categories
        $this->activeCategories = $this->getActiveCategories($customCategories);

        // Then, fetch the inactive categories
        $inactiveCategories = $this->getInactiveCategories($customCategories);

        // Generate active categories
        if ($this->activeCategories !== null) {
            $this->Template->activeCategories = $this->renderArticleCategories($rootCategoryId, $this->activeCategories->fetchEach('id'), true);

            // Add the canonical URL tag
            if ($this->article_enableCanonicalUrls) {
                $GLOBALS['TL_HEAD'][] = sprintf('<link rel="canonical" href="%s">', $GLOBALS['objPage']->getAbsoluteUrl());
            }

            // Add the "reset categories" link
            if ($this->article_resetCategories) {
                $this->Template->resetUrl = $this->getTargetPage()->getFrontendUrl();
            }
        } else {
            $this->Template->activeCategories = '';
            $this->Template->resetUrl = false;
        }

        // Generate inactive categories
        if ($inactiveCategories !== null) {
            $this->Template->inactiveCategories = $this->renderArticleCategories($rootCategoryId, $inactiveCategories->fetchEach('id'));
        } else {
            $this->Template->inactiveCategories = '';
        }
    }

    /**
     * Get the active categories
     *
     * @param array $customCategories
     *
     * @return Collection|null
     */
    protected function getActiveCategories(array $customCategories = [])
    {
        $param = System::getContainer()->get('codefog_article_categories.manager')->getParameterName();

        if (!($aliases = Input::get($param))) {
            return null;
        }

        $aliases = StringUtil::trimsplit(static::getCategorySeparator(), $aliases);
        $aliases = array_unique(array_filter($aliases));

        if (count($aliases) === 0) {
            return null;
        }

        // Get the categories that do have article assigned
        $models = ArticleCategoryModel::findPublishedByArchives($this->article_archives, $customCategories, $aliases);

        // No models have been found but there are some aliases present
        if ($models === null && count($aliases) !== 0) {
            Controller::redirect($this->getTargetPage()->getFrontendUrl());
        }

        // Validate the provided aliases with the categories found
        if ($models !== null) {
            $realAliases = [];

            /** @var ArticleCategoryModel $model */
            foreach ($models as $model) {
                $realAliases[] = $this->manager->getCategoryAlias($model, $GLOBALS['objPage']);
            }

            if (count(array_diff($aliases, $realAliases)) > 0) {
                Controller::redirect($this->getTargetPage()->getFrontendUrl(sprintf(
                    '/%s/%s',
                    $this->manager->getParameterName($GLOBALS['objPage']->rootId),
                    implode(static::getCategorySeparator(), $realAliases)
                )));
            }
        }

        return $models;
    }

    /**
     * Get the inactive categories
     *
     * @param array $customCategories
     *
     * @return Collection|null
     */
    protected function getInactiveCategories(array $customCategories = [])
    {
        $excludedIds = [];

        // Find only the categories that still can display some results combined with active categories
        if ($this->activeCategories !== null) {
            // Union filtering
            if ($this->article_filterCategoriesUnion) {
                $excludedIds = $this->activeCategories->fetchEach('id');
            } else {
                // Intersection filtering
                $columns = [];
                $values = [];

                // Collect the article that match all active categories
                /** @var ArticleCategoryModel $activeCategory */
                foreach ($this->activeCategories as $activeCategory) {
                    $criteria = new ArticleCriteria(System::getContainer()->get('contao.framework'));

                    try {
                        $criteria->setBasicCriteria($this->article_archives);
                        $criteria->setCategory($activeCategory->id, false, (bool) $this->article_includeSubcategories);
                    } catch (NoArticlesException $e) {
                        continue;
                    }

                    $columns = array_merge($columns, $criteria->getColumns());
                    $values = array_merge($values, $criteria->getValues());
                }

                // Should not happen but you never know
                if (count($columns) === 0) {
                    return null;
                }

                $articleIds = Database::getInstance()
                    ->prepare('SELECT id FROM tl_article WHERE ' . implode(' AND ', $columns))
                    ->execute($values)
                    ->fetchEach('id')
                ;

                if (count($articleIds) === 0) {
                    return null;
                }

                $categoryIds = Model::getRelatedValues('tl_article', 'categories', $articleIds);
                $categoryIds = \array_map('intval', $categoryIds);
                $categoryIds = \array_unique(\array_filter($categoryIds));

                // Include the parent categories
                if ($this->article_includeSubcategories) {
                    foreach ($categoryIds as $categoryId) {
                        $categoryIds = array_merge($categoryIds, \array_map('intval', Database::getInstance()->getParentRecords($categoryId, 'tl_article_category')));
                    }
                }

                // Remove the active categories, so they are not considered again
                $categoryIds = array_diff($categoryIds, $this->activeCategories->fetchEach('id'));

                // Filter by custom categories
                if (count($customCategories) > 0) {
                    $categoryIds = array_intersect($categoryIds, $customCategories);
                }

                $categoryIds = array_values(array_unique($categoryIds));

                if (count($categoryIds) === 0) {
                    return null;
                }

                $customCategories = $categoryIds;
            }
        }

        return ArticleCategoryModel::findPublishedByArchives($this->article_archives, $customCategories, [], $excludedIds);
    }

    /**
     * Get the target page.
     *
     * @return PageModel
     */
    protected function getTargetPage()
    {
        static $page;

        if (null === $page) {
            if ($this->jumpTo > 0
                && (int) $GLOBALS['objPage']->id !== (int) $this->jumpTo
                && null !== ($target = PageModel::findPublishedById($this->jumpTo))
            ) {
                $page = $target;
            } else {
                $page = $GLOBALS['objPage'];
            }
        }

        return $page;
    }

    /**
     * Get the category IDs of the current article item.
     *
     * @return array
     */
    protected function getCurrentArticleCategories()
    {
        if (!($alias = Input::getAutoItem('items', false, true))
            || null === ($article = ArticleModel::findPublishedByParentAndIdOrAlias($alias, $this->article_archives))
        ) {
            return [];
        }

        $ids = Model::getRelatedValues('tl_article', 'categories', $article->id);
        $ids = \array_map('intval', \array_unique($ids));

        return $ids;
    }

    /**
     * Recursively compile the article categories and return it as HTML string.
     *
     * @param int   $pid
     * @param array $ids
     * @param bool  $isActiveCategories
     *
     * @return string
     */
    protected function renderArticleCategories($pid, array $ids, $isActiveCategories = false)
    {
        if (null === ($categories = ArticleCategoryModel::findPublishedByIds($ids, $pid))) {
            return '';
        }

        // Layout template fallback
        if (!$this->navigationTpl) {
            $this->navigationTpl = 'nav_articlecategories';
        }

        $template = new FrontendTemplate($this->navigationTpl);
        $template->type = \get_class($this);
        $template->cssID = $this->cssID;
        $template->level = 'level_1';
        $template->showQuantity = $isActiveCategories ? false : (bool) $this->article_showQuantity;
        $template->isActiveCategories = $isActiveCategories;

        $items = [];
        $activeAliases = [];

        // Collect the active category parameters
        if ($this->activeCategories !== null) {
            /** @var ArticleCategoryModel $activeCategory */
            foreach ($this->activeCategories as $activeCategory) {
                $activeAliases[] = $this->manager->getCategoryAlias($activeCategory, $GLOBALS['objPage']);
            }
        }

        $resetUrl = $this->getTargetPage()->getFrontendUrl();
        $parameterName = $this->manager->getParameterName($GLOBALS['objPage']->rootId);

        /** @var ArticleCategoryModel $category */
        foreach ($categories as $category) {
            $categoryAlias = $this->manager->getCategoryAlias($category, $GLOBALS['objPage']);

            // Add/remove the category alias to the active ones
            if (in_array($categoryAlias, $activeAliases, true)) {
                $aliases = array_diff($activeAliases, [$categoryAlias]);
            } else {
                $aliases = array_merge($activeAliases, [$categoryAlias]);
            }

            // Generate the category URL if there are any aliases to add, otherwise use the reset URL
            if (count($aliases) > 0) {
                $url = $this->getTargetPage()->getFrontendUrl(sprintf('/%s/%s', $parameterName, implode(static::getCategorySeparator(), $aliases)));
            } else {
                $url = $resetUrl;
            }

            $items[] = $this->generateItem(
                $url,
                $category->getTitle(),
                $category->getTitle(),
                $this->generateItemCssClass($category),
                in_array($categoryAlias, $activeAliases, true),
                $category
            );
        }

        // Add first/last/even/odd classes
        RowClass::withKey('class')->addFirstLast()->addEvenOdd()->applyTo($items);

        $template->items = $items;

        return $template->parse();
    }

    /**
     * Generate the item.
     *
     * @param string                 $url
     * @param string                 $link
     * @param string                 $title
     * @param string                 $cssClass
     * @param bool                   $isActive
     * @param ArticleCategoryModel|null $category
     *
     * @return array
     */
    protected function generateItem($url, $link, $title, $cssClass, $isActive, ArticleCategoryModel $category = null)
    {
        $data = [];

        // Set the data from category
        if (null !== $category) {
            $data = $category->row();
        }

        $data['isActive'] = $isActive;
        $data['subitems'] = '';
        $data['class'] = $cssClass;
        $data['title'] = StringUtil::specialchars($title);
        $data['linkTitle'] = StringUtil::specialchars($title);
        $data['link'] = $link;
        $data['href'] = ampersand($url);
        $data['quantity'] = 0;

        // Add the "active" class
        if ($isActive) {
            $data['class'] = \trim($data['class'].' active');
        }

        // Add the article quantity
        if ($this->article_showQuantity) {
            if (null === $category) {
                $data['quantity'] = ArticleCategoryModel::getUsage($this->article_archives, null, false, [], (bool) $this->article_filterCategoriesUnion);
            } else {
                $data['quantity'] = ArticleCategoryModel::getUsage(
                    $this->article_archives,
                    $category->id,
                    (bool) $this->article_includeSubcategories,
                    ($this->activeCategories !== null) ? $this->activeCategories->fetchEach('id') : [],
                    (bool) $this->article_filterCategoriesUnion
                );
            }
        }

        // Add the image
        if (null !== $category && null !== ($image = $this->manager->getImage($category))) {
            $data['image'] = new \stdClass();
            Controller::addImageToTemplate($data['image'], [
                'singleSRC' => $image->path,
                'size' => $this->article_categoryImgSize,
                'alt' => $title,
                'imageTitle' => $title,
            ]);
        } else {
            $data['image'] = null;
        }

        return $data;
    }

    /**
     * Generate the item CSS class.
     *
     * @param ArticleCategoryModel $category
     *
     * @return string
     */
    protected function generateItemCssClass(ArticleCategoryModel $category)
    {
        $cssClasses = [$category->getCssClass()];

        // Add the trail class
        if (\in_array((int) $category->id, $this->manager->getTrailIds($category), true)) {
            $cssClasses[] = 'trail';
        }

        // Add the article trail class
        if (\in_array((int) $category->id, $this->currentArticleCategories, true)) {
            $cssClasses[] = 'article_trail';
        }

        return \implode(' ', $cssClasses);
    }
}
