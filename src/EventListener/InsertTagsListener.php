<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\EventListener;

use Petzka\DemoBundle\Model\ArticleCategoryModel;
use Petzka\DemoBundle\ArticleCategoriesManager;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Input;
use Contao\StringUtil;

class InsertTagsListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var ArticleCategoriesManager
     */
    private $manager;

    /**
     * InsertTagsListener constructor.
     *
     * @param ArticleCategoriesManager $manager
     */
    public function __construct(ArticleCategoriesManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * On replace the insert tags.
     *
     * @param string $tag
     *
     * @return string|bool
     */
    public function onReplace($tag)
    {
        $chunks = trimsplit('::', $tag);

        if ('article_categories' === $chunks[0]) {
            /** @var Input $input */
            $input = $this->framework->getAdapter(Input::class);

            if ($alias = $input->get($this->manager->getParameterName())) {
                /** @var ArticleCategoryModel $model */
                $model = $this->framework->getAdapter(ArticleCategoryModel::class);

                if (null !== ($category = $model->findPublishedByIdOrAlias($alias))) {
                    $value = $category->{$chunks[1]};

                    // Convert the binary to UUID for images (#147)
                    if ($chunks[1] === 'image' && $value) {
                        return StringUtil::binToUuid($value);
                    }

                    return $value;
                }
            }
        }

        return false;
    }
}
