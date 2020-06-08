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
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;
use Terminal42\DcMultilingualBundle\Model\Multilingual;

class ChangeLanguageListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var ArticleCategoriesManager
     */
    private $manager;

    /**
     * ChangeLanguageListener constructor.
     *
     * @param ArticleCategoriesManager $manager
     */
    public function __construct(ArticleCategoriesManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * On change language navigation.
     *
     * @param ChangelanguageNavigationEvent $event
     */
    public function onChangelanguageNavigation(ChangelanguageNavigationEvent $event)
    {
        $this->updateAlias($event);
        $this->updateParameter($event);
    }

    /**
     * Update the category alias value.
     *
     * @param ChangelanguageNavigationEvent $event
     */
    private function updateAlias(ChangelanguageNavigationEvent $event)
    {
        /** @var ArticleCategoryModel $modelAdapter */
        $modelAdapter = $this->framework->getAdapter(ArticleCategoryModel::class);

        $param = $this->manager->getParameterName();

        if (!($alias = $event->getUrlParameterBag()->getUrlAttribute($param))) {
            return;
        }

        $model = $modelAdapter->findPublishedByIdOrAlias($alias);

        // Set the alias only for multilingual models
        if (null !== $model && $model instanceof Multilingual) {
            $event->getUrlParameterBag()->setUrlAttribute(
                $param,
                $model->getAlias($event->getNavigationItem()->getRootPage()->rootLanguage)
            );
        }
    }

    /**
     * Update the parameter name.
     *
     * @param ChangelanguageNavigationEvent $event
     */
    private function updateParameter(ChangelanguageNavigationEvent $event)
    {
        $currentParam = $this->manager->getParameterName();
        $newParam = $this->manager->getParameterName($event->getNavigationItem()->getRootPage()->id);

        $parameters = $event->getUrlParameterBag();
        $attributes = $parameters->getUrlAttributes();

        if (!isset($attributes[$currentParam])) {
            return;
        }

        $attributes[$newParam] = $attributes[$currentParam];
        unset($attributes[$currentParam]);

        $parameters->setUrlAttributes($attributes);
    }
}
