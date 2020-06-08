<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle;

use Petzka\DemoBundle\Model\ArticleCategoryModel;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Database;
use Contao\Module;
use Contao\ModuleArticleList;
use Contao\PageModel;

class ArticleCategoriesManager implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * Generate the category URL.
     *
     * @param ArticleCategoryModel $category
     * @param PageModel         $page
     * @param bool              $absolute
     *
     * @return string
     */
    public function generateUrl(ArticleCategoryModel $category, PageModel $page, $absolute = false)
    {
        $page->loadDetails();

        $params = '/'.$this->getParameterName($page->rootId).'/'.$this->getCategoryAlias($category, $page);

        return $absolute ? $page->getAbsoluteUrl($params) : $page->getFrontendUrl($params);
    }

    /**
     * Get the image.
     *
     * @param ArticleCategoryModel $category
     *
     * @return \Contao\FilesModel|null
     */
    public function getImage(ArticleCategoryModel $category)
    {
        if (null === ($image = $category->getImage()) || !\is_file(TL_ROOT.'/'.$image->path)) {
            return null;
        }

        return $image;
    }

    /**
     * Get the category alias
     *
     * @param ArticleCategoryModel $category
     * @param PageModel         $page
     *
     * @return string
     */
    public function getCategoryAlias(ArticleCategoryModel $category, PageModel $page)
    {
        return $category->alias;
    }

    /**
     * Get the parameter name.
     *
     * @param int|null $rootId
     *
     * @return string
     */
    public function getParameterName($rootId = null)
    {
        $rootId = $rootId ?: $GLOBALS['objPage']->rootId;

        if (!$rootId || null === ($rootPage = PageModel::findByPk($rootId))) {
            return '';
        }

        return $rootPage->articleCategories_param ?: 'category';
    }

    /**
     * Get the category target page.
     *
     * @param ArticleCategoryModel $category
     *
     * @return PageModel|null
     */
    public function getTargetPage(ArticleCategoryModel $category)
    {
        $pageId = $category->jumpTo;

        // Inherit the page from parent if there is none set
        if (!$pageId) {
            $pid = $category->pid;

            do {
                /** @var ArticleCategoryModel $parent */
                $parent = $category->findByPk($pid);

                if (null !== $parent) {
                    $pid = $parent->pid;
                    $pageId = $parent->jumpTo;
                }
            } while ($pid && !$pageId);
        }

        // Get the page model
        if ($pageId) {
            /** @var PageModel $pageAdapter */
            $pageAdapter = $this->framework->getAdapter(PageModel::class);

            return $pageAdapter->findPublishedById($pageId);
        }

        return null;
    }

    /**
     * Get the category trail IDs.
     *
     * @param ArticleCategoryModel $category
     *
     * @return array
     */
    public function getTrailIds(ArticleCategoryModel $category)
    {
        static $cache;

        if (!isset($cache[$category->id])) {
            /** @var Database $db */
            $db = $this->framework->createInstance(Database::class);

            $ids = $db->getParentRecords($category->id, $category->getTable());
            $ids = \array_map('intval', \array_unique($ids));

            // Remove the current category
            unset($ids[\array_search($category->id, $ids, true)]);

            $cache[$category->id] = $ids;
        }

        return $cache[$category->id];
    }

    /**
     * Return true if the category is visible for module.
     *
     * @param ArticleCategoryModel $category
     * @param Module            $module
     *
     * @return bool
     */
    public function isVisibleForModule(ArticleCategoryModel $category, Module $module)
    {
        // List module
        if ($category->hideInList && ($module instanceof ModuleArticleList)) {
            return false;
        }

        return true;
    }
}
