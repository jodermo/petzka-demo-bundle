<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Criteria;

use Petzka\DemoBundle\Exception\CategoryNotFoundException;
use Petzka\DemoBundle\Exception\NoArticlesException;
use Petzka\DemoBundle\FrontendModule\CumulativeFilterModule;
use Petzka\DemoBundle\Model\ArticleCategoryModel;
use Petzka\DemoBundle\ArticleCategoriesManager;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Database;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Haste\Model\Model;

class ArticleCriteriaBuilder implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var ArticleCategoriesManager
     */
    private $manager;

    /**
     * ArticleCriteriaBuilder constructor.
     *
     * @param Connection            $db
     * @param ArticleCategoriesManager $manager
     */
    public function __construct(Connection $db, ArticleCategoriesManager $manager)
    {
        $this->db = $db;
        $this->manager = $manager;
    }

    /**
     * Get the criteria for archive module.
     *
     * @param array  $archives
     * @param int    $begin
     * @param int    $end
     * @param Module $module
     *
     * @return ArticleCriteria|null
     */
    public function getCriteriaForArchiveModule(array $archives, $begin, $end, Module $module)
    {
        $criteria = new ArticleCriteria($this->framework);

        try {
            $criteria->setBasicCriteria($archives, $module->article_order);

            // Set the time frame
            $criteria->setTimeFrame($begin, $end);

            // Set the regular list criteria
            $this->setRegularListCriteria($criteria, $module);
        } catch (NoArticlesException $e) {
            return null;
        }

        return $criteria;
    }

    /**
     * Get the criteria for list module.
     *
     * @param array     $archives
     * @param bool|null $featured
     * @param Module    $module
     *
     * @return ArticleCriteria|null
     */
    public function getCriteriaForListModule(array $archives, $featured, Module $module)
    {
        $criteria = new ArticleCriteria($this->framework);

        try {
            $criteria->setBasicCriteria($archives, $module->article_order, $module->article_featured);

            // Set the featured filter
            if (null !== $featured) {
                $criteria->setFeatured($featured);
            }

            // Set the criteria for related categories
            if ($module->article_relatedCategories) {
                $this->setRelatedListCriteria($criteria, $module);
            } else {
                // Set the regular list criteria
                $this->setRegularListCriteria($criteria, $module);
            }
        } catch (NoArticlesException $e) {
            return null;
        }

        return $criteria;
    }

    /**
     * Get the criteria for menu module.
     *
     * @param array  $archives
     * @param Module $module
     *
     * @return ArticleCriteria|null
     */
    public function getCriteriaForMenuModule(array $archives, Module $module)
    {
        $criteria = new ArticleCriteria($this->framework);

        try {
            $criteria->setBasicCriteria($archives, $module->article_order);

            // Set the regular list criteria
            $this->setRegularListCriteria($criteria, $module);
        } catch (NoArticlesException $e) {
            return null;
        }

        return $criteria;
    }

    /**
     * Set the regular list criteria.
     *
     * @param ArticleCriteria $criteria
     * @param Module       $module
     *
     * @throws CategoryNotFoundException
     * @throws NoArticlesException
     */
    private function setRegularListCriteria(ArticleCriteria $criteria, Module $module)
    {
        // Filter by default categories
        if (\count($default = StringUtil::deserialize($module->article_filterDefault, true)) > 0) {
            $criteria->setDefaultCategories($default);
        }

        // Filter by multiple active categories
        if ($module->article_filterCategoriesCumulative) {
            /** @var Input $input */
            $input = $this->framework->getAdapter(Input::class);
            $param = $this->manager->getParameterName();

            if ($aliases = $input->get($param)) {
                $aliases = StringUtil::trimsplit(CumulativeFilterModule::getCategorySeparator(), $aliases);
                $aliases = array_unique(array_filter($aliases));

                if (count($aliases) > 0) {
                    /** @var ArticleCategoryModel $model */
                    $model = $this->framework->getAdapter(ArticleCategoryModel::class);
                    $categories = [];

                    foreach ($aliases as $alias) {
                        // Return null if the category does not exist
                        if (null === ($category = $model->findPublishedByIdOrAlias($alias))) {
                            throw new CategoryNotFoundException(sprintf('Article category "%s" was not found', $alias));
                        }

                        $categories[] = (int) $category->id;
                    }

                    if (count($categories) > 0) {
                        // Union filtering
                        if ($module->article_filterCategoriesUnion) {
                            $criteria->setCategories($categories, (bool) $module->article_filterPreserve, (bool) $module->article_includeSubcategories);
                        } else {
                            // Intersection filtering
                            foreach ($categories as $category) {
                                $criteria->setCategory($category, (bool) $module->article_filterPreserve, (bool) $module->article_includeSubcategories);
                            }
                        }
                    }
                }
            }

            return;
        }

        // Filter by active category
        if ($module->article_filterCategories) {
            /** @var Input $input */
            $input = $this->framework->getAdapter(Input::class);
            $param = $this->manager->getParameterName();

            if ($alias = $input->get($param)) {
                /** @var ArticleCategoryModel $model */
                $model = $this->framework->getAdapter(ArticleCategoryModel::class);

                // Return null if the category does not exist
                if (null === ($category = $model->findPublishedByIdOrAlias($alias))) {
                    throw new CategoryNotFoundException(sprintf('Article category "%s" was not found', $alias));
                }

                $criteria->setCategory($category->id, (bool) $module->article_filterPreserve, (bool) $module->article_includeSubcategories);
            }
        }
    }

    /**
     * Set the related list criteria.
     *
     * @param ArticleCriteria $criteria
     * @param Module       $module
     *
     * @throws NoArticlesException
     */
    private function setRelatedListCriteria(ArticleCriteria $criteria, Module $module)
    {
        if (null === ($article = $module->currentArticle)) {
            throw new NoArticlesException();
        }

        /** @var Model $adapter */
        $adapter = $this->framework->getAdapter(Model::class);
        $categories = \array_unique($adapter->getRelatedValues($article->getTable(), 'categories', $article->id));

        // This article has no article categories assigned
        if (0 === \count($categories)) {
            throw new NoArticlesException();
        }

        $categories = \array_map('intval', $categories);
        $excluded = $this->db->fetchAll('SELECT id FROM tl_article_category WHERE excludeInRelated=1');

        // Exclude the categories
        foreach ($excluded as $category) {
            if (false !== ($index = \array_search((int) $category['id'], $categories, true))) {
                unset($categories[$index]);
            }
        }

        // Exclude categories by root
        if ($module->article_categoriesRoot > 0) {
            $categories = array_intersect($categories, ArticleCategoryModel::getAllSubcategoriesIds($module->article_categoriesRoot));
        }

        // There are no categories left
        if (0 === \count($categories)) {
            throw new NoArticlesException();
        }

        $criteria->setDefaultCategories($categories, (bool) $module->article_includeSubcategories, $module->article_relatedCategoriesOrder);
        $criteria->setExcludedArticle([$article->id]);
    }
}
