<?php


/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Picker;

use Petzka\DemoBundle\PermissionChecker;
use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;

class ArticleCategoriesPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param PermissionChecker $permissionChecker
     */
    public function setPermissionChecker(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getDcaTable()
    {
        return 'tl_article_category';
    }

    /**
     * {@inheritdoc}
     */
    public function getDcaAttributes(PickerConfig $config)
    {
        $attributes = ['fieldType' => 'checkbox'];

        if ($fieldType = $config->getExtra('fieldType')) {
            $attributes['fieldType'] = $fieldType;
        }

        if ($this->supportsValue($config)) {
            $attributes['value'] = \array_map('intval', \explode(',', $config->getValue()));
        }

        if (\is_array($rootNodes = $config->getExtra('rootNodes'))) {
            $attributes['rootNodes'] = $rootNodes;
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(PickerConfig $config)
    {
        // Set the article categories root in session for further reference in onload_callback (see #137)
        if (\is_array($rootNodes = $config->getExtra('rootNodes'))) {
            $_SESSION['ARTICLE_CATEGORIES_ROOT'] = $rootNodes;
        } else {
            unset($_SESSION['ARTICLE_CATEGORIES_ROOT']);
        }

        return parent::getUrl($config);
    }

    /**
     * {@inheritdoc}
     */
    public function convertDcaValue(PickerConfig $config, $value)
    {
        return (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'articleCategoriesPicker';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsContext($context)
    {
        if ($this->permissionChecker === null) {
            return false;
        }

        return 'articleCategories' === $context && ($this->permissionChecker->canUserManageCategories() || $this->permissionChecker->canUserAssignCategories());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(PickerConfig $config)
    {
        foreach (\explode(',', $config->getValue()) as $id) {
            if (!\is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteParameters(PickerConfig $config = null)
    {
        return ['do' => 'article', 'table' => 'tl_article_category'];
    }
}
