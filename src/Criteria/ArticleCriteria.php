<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */


namespace Petzka\DemoBundle\Criteria;

use Petzka\DemoBundle\Exception\NoArticlesException;
use Petzka\DemoBundle\Model\ArticleCategoryModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Database;
use Contao\Date;
use Contao\ArticleModel;
use Haste\Model\Model;

class ArticleCriteria
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * ArticleCriteria constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Set the basic criteria.
     *
     * @param array  $archives
     * @param string $sorting
     *
     * @throws NoArticlesException
     */
    public function setBasicCriteria(array $archives, $sorting = null, $featured = null)
    {
        $archives = $this->parseIds($archives);

        if (0 === \count($archives)) {
            throw new NoArticlesException();
        }

        $t = $this->getArticleModelAdapter()->getTable();

        $this->columns[] = "$t.pid IN(".\implode(',', \array_map('intval', $archives)).')';

        $order = '';

        if ('featured_first' === $featured) {
            $order .= "$t.featured DESC, ";
        }

        // Set the sorting
        switch ($sorting) {
            case 'order_headline_asc':
                $order .= "$t.headline";
                break;
            case 'order_headline_desc':
                $order .= "$t.headline DESC";
                break;
            case 'order_random':
                $order .= 'RAND()';
                break;
            case 'order_date_asc':
                $order .= "$t.date";
                break;
            default:
                $order .= "$t.date DESC";
                break;
        }

        $this->options['order'] = $order;

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE === 'BE') {
            /** @var Date $dateAdapter */
            $dateAdapter = $this->framework->getAdapter(Date::class);

            $time = $dateAdapter->floorToMinute();
            $this->columns[] = "($t.start=? OR $t.start<=?) AND ($t.stop=? OR $t.stop>?) AND $t.published=?";
            $this->values = \array_merge($this->values, ['', $time, '', ($time + 60), 1]);
        }
    }

    /**
     * Set the features items.
     *
     * @param bool $enable
     */
    public function setFeatured($enable)
    {
        $t = $this->getArticleModelAdapter()->getTable();

        if (true === $enable) {
            $this->columns[] = "$t.featured=?";
            $this->values[] = 1;
        } elseif (false === $enable) {
            $this->columns[] = "$t.featured=?";
            $this->values[] = '';
        }
    }

    /**
     * Set the time frame.
     *
     * @param int $begin
     * @param int $end
     */
    public function setTimeFrame($begin, $end)
    {
        $t = $this->getArticleModelAdapter()->getTable();

        $this->columns[] = "$t.date>=? AND $t.date<=?";
        $this->values[] = $begin;
        $this->values[] = $end;
    }

    /**
     * Set the default categories.
     *
     * @param array       $defaultCategories
     * @param bool        $includeSubcategories
     * @param string|null $order
     *
     * @throws NoArticlesException
     */
    public function setDefaultCategories(array $defaultCategories, $includeSubcategories = true, $order = null)
    {
        $defaultCategories = $this->parseIds($defaultCategories);

        if (0 === \count($defaultCategories)) {
            throw new NoArticlesException();
        }

        // Include the subcategories
        if ($includeSubcategories) {
            /** @var ArticleCategoryModel $articleCategoryModel */
            $articleCategoryModel = $this->framework->getAdapter(ArticleCategoryModel::class);
            $defaultCategories = $articleCategoryModel->getAllSubcategoriesIds($defaultCategories);
        }

        /** @var Model $model */
        $model = $this->framework->getAdapter(Model::class);

        $articleIds = $model->getReferenceValues('tl_article', 'categories', $defaultCategories);
        $articleIds = $this->parseIds($articleIds);

        if (0 === \count($articleIds)) {
            throw new NoArticlesException();
        }

        $t = $this->getArticleModelAdapter()->getTable();

        $this->columns['defaultCategories'] = "$t.id IN(".\implode(',', $articleIds).')';

        // Order article items by best match
        if ($order === 'best_match') {
            $mapper = [];

            // Build the mapper
            foreach (array_unique($articleIds) as $articleId) {
                $mapper[$articleId] = count(array_intersect($defaultCategories, array_unique($model->getRelatedValues($t, 'categories', $articleId))));
            }

            arsort($mapper);

            $this->options['order'] = Database::getInstance()->findInSet("$t.id", array_keys($mapper));
        }
    }

    /**
     * Set the category (intersection filtering).
     *
     * @param int  $category
     * @param bool $preserveDefault
     * @param bool $includeSubcategories
     *
     * @throws NoArticlesException
     */
    public function setCategory($category, $preserveDefault = false, $includeSubcategories = false)
    {
        /** @var Model $model */
        $model = $this->framework->getAdapter(Model::class);

        // Include the subcategories
        if ($includeSubcategories) {
            /** @var ArticleCategoryModel $articleCategoryModel */
            $articleCategoryModel = $this->framework->getAdapter(ArticleCategoryModel::class);
            $category = $articleCategoryModel->getAllSubcategoriesIds($category);
        }

        $articleIds = $model->getReferenceValues('tl_article', 'categories', $category);
        $articleIds = $this->parseIds($articleIds);

        if (0 === \count($articleIds)) {
            throw new NoArticlesException();
        }

        // Do not preserve the default categories
        if (!$preserveDefault) {
            unset($this->columns['defaultCategories']);
        }

        $t = $this->getArticleModelAdapter()->getTable();

        $this->columns[] = "$t.id IN(".\implode(',', $articleIds).')';
    }

    /**
     * Set the categories (union filtering).
     *
     * @param array $categories
     * @param bool  $preserveDefault
     * @param bool  $includeSubcategories
     *
     * @throws NoArticlesException
     */
    public function setCategories($categories, $preserveDefault = false, $includeSubcategories = false)
    {
        $allArticleIds = [];

        /** @var Model $model */
        $model = $this->framework->getAdapter(Model::class);

        foreach ($categories as $category) {
            // Include the subcategories
            if ($includeSubcategories) {
                /** @var ArticleCategoryModel $articleCategoryModel */
                $articleCategoryModel = $this->framework->getAdapter(ArticleCategoryModel::class);
                $category = $articleCategoryModel->getAllSubcategoriesIds($category);
            }

            $articleIds = $model->getReferenceValues('tl_article', 'categories', $category);
            $articleIds = $this->parseIds($articleIds);

            if (0 === \count($articleIds)) {
                continue;
            }

            $allArticleIds = array_merge($allArticleIds, $articleIds);
        }

        if (\count($allArticleIds) === 0) {
            throw new NoArticlesException();
        }

        // Do not preserve the default categories
        if (!$preserveDefault) {
            unset($this->columns['defaultCategories']);
        }

        $t = $this->getArticleModelAdapter()->getTable();

            $allArticleIds = array_merge($allArticleIds, $articleIds);
        $this->columns[] = "$t.id IN(".\implode(',', $allArticleIds).')';
    }

    /**
     * Set the excluded article IDs.
     *
     * @param array $articleIds
     */
    public function setExcludedArticle(array $articleIds)
    {
        $articleIds = $this->parseIds($articleIds);

        if (0 === \count($articleIds)) {
            throw new NoArticlesException();
        }

        $t = $this->getArticleModelAdapter()->getTable();

        $this->columns[] = "$t.id NOT IN (".\implode(',', $articleIds).')';
    }

    /**
     * Set the limit.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->options['limit'] = $limit;
    }

    /**
     * Set the offset.
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->options['offset'] = $offset;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the article model adapter.
     *
     * @return ArticleModel
     */
    public function getArticleModelAdapter()
    {
        /** @var ArticleModel $adapter */
        $adapter = $this->framework->getAdapter(ArticleModel::class);

        return $adapter;
    }

    /**
     * Parse the record IDs.
     *
     * @param array $ids
     *
     * @return array
     */
    private function parseIds(array $ids)
    {
        $ids = \array_map('intval', $ids);
        $ids = \array_filter($ids);
        $ids = \array_unique($ids);

        return \array_values($ids);
    }
}
