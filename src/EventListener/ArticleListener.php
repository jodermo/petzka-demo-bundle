<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\EventListener;

use Petzka\DemoBundle\Criteria\ArticleCriteria;
use Petzka\DemoBundle\Criteria\ArticleCriteriaBuilder;
use Petzka\DemoBundle\Exception\CategoryNotFoundException;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Model\Collection;
use Contao\ModuleArticleList;

class ArticleListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var \Codefog\DemoBundle\Criteria\ArticleCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * InsertTagsListener constructor.
     *
     * @param \Codefog\DemoBundle\Criteria\ArticleCriteriaBuilder $searchBuilder
     */
    public function __construct(ArticleCriteriaBuilder $searchBuilder)
    {
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * On article list count items.
     *
     * @param array          $archives
     * @param bool|null      $featured
     * @param ModuleArticleList $module
     *
     * @return int
     */
    public function onArticleListCountItems(array $archives, $featured, ModuleArticleList $module)
    {
        if (null === ($criteria = $this->getCriteria($archives, $featured, $module))) {
            return 0;
        }

        return $criteria->getArticleModelAdapter()->countBy($criteria->getColumns(), $criteria->getValues());
    }

    /**
     * On article list fetch items.
     *
     * @param array          $archives
     * @param bool|null      $featured
     * @param int            $limit
     * @param int            $offset
     * @param ModuleArticleList $module
     *
     * @return Collection|null
     */
    public function onArticleListFetchItems(array $archives, $featured, $limit, $offset, ModuleArticleList $module)
    {
        if (null === ($criteria = $this->getCriteria($archives, $featured, $module))) {
            return null;
        }

        $criteria->setLimit($limit);
        $criteria->setOffset($offset);

        return $criteria->getArticleModelAdapter()->findBy(
            $criteria->getColumns(),
            $criteria->getValues(),
            $criteria->getOptions()
        );
    }

    /**
     * Get the criteria.
     *
     * @param array          $archives
     * @param bool|null      $featured
     * @param ModuleArticleList $module
     *
     * @return ArticleCriteria|null
     *
     * @throws PageNotFoundException
     */
    private function getCriteria(array $archives, $featured, ModuleArticleList $module)
    {
        try {
            $criteria = $this->searchBuilder->getCriteriaForListModule($archives, $featured, $module);
        } catch (CategoryNotFoundException $e) {
            throw new PageNotFoundException($e->getMessage());
        }

        return $criteria;
    }
}
