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
use Contao\Controller;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\FrontendTemplate;
use Contao\Model\Collection;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;

class TemplateListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var ArticleCategoriesManager
     */
    private $manager;

    /**
     * TemplateListener constructor.
     *
     * @param ArticleCategoriesManager $manager
     */
    public function __construct(ArticleCategoriesManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * On parse the articles.
     *
     * @param FrontendTemplate $template
     * @param array            $data
     * @param Module           $module
     */
    public function onParseArticles(FrontendTemplate $template, array $data, Module $module)
    {
        /** @var ArticleCategoryModel $articleCategoryModelAdapter */
        $articleCategoryModelAdapter = $this->framework->getAdapter(ArticleCategoryModel::class);

        if (null === ($models = $articleCategoryModelAdapter->findPublishedByArticle($data['id']))) {
            return;
        }

        $this->addCategoriesToTemplate($template, $module, $models);
    }

    /**
     * Add categories to the template.
     *
     * @param FrontendTemplate $template
     * @param Module           $module
     * @param Collection       $categories
     */
    private function addCategoriesToTemplate(FrontendTemplate $template, Module $module, Collection $categories)
    {
        $data = [];
        $list = [];
        $cssClasses = trimsplit(' ', $template->class);

        /** @var ArticleCategoryModel $category */
        foreach ($categories as $category) {
            // Skip the categories not eligible for the current module
            if (!$this->manager->isVisibleForModule($category, $module)) {
                continue;
            }

            // Add category to data and list
            $data[$category->id] = $this->generateCategoryData($category, $module);
            $list[$category->id] = $category->getTitle();

            // Add the category CSS classes to article class
            $cssClasses = \array_merge($cssClasses, trimsplit(' ', $category->getCssClass()));
        }

        // Sort the categories data alphabetically
        \uasort($data, function ($a, $b) {
            return \strnatcasecmp($a['name'], $b['name']);
        });

        // Sort the category list alphabetically
        \asort($list);

        $template->categories = $data;
        $template->categoriesList = $list;

        if (count($cssClasses = \array_unique($cssClasses)) > 0) {
            $template->class = ' ' . \implode(' ', $cssClasses);
        }
    }

    /**
     * Generate the category data.
     *
     * @param ArticleCategoryModel $category
     * @param Module            $module
     *
     * @return array
     */
    private function generateCategoryData(ArticleCategoryModel $category, Module $module)
    {
        $data = $category->row();

        $data['model'] = $category;
        $data['name'] = $category->getTitle();
        $data['class'] = $category->getCssClass();
        $data['href'] = '';
        $data['hrefWithParam'] = '';
        $data['targetPage'] = null;

        /** @var StringUtil $stringUtilAdapter */
        $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);
        $data['linkTitle'] = $stringUtilAdapter->specialchars($data['name']);

        /** @var PageModel $pageAdapter */
        $pageAdapter = $this->framework->getAdapter(PageModel::class);

        // Overwrite the category links with filter page set in module
        if ($module->article_categoryFilterPage && null !== ($targetPage = $pageAdapter->findPublishedById($module->article_categoryFilterPage))) {
            $data['href'] = $this->manager->generateUrl($category, $targetPage);
            $data['hrefWithParam'] = $data['href'];
            $data['targetPage'] = $targetPage;
        } elseif (null !== ($targetPage = $this->manager->getTargetPage($category))) {
            // Add the category target page and URLs
            $data['href'] = $targetPage->getFrontendUrl();
            $data['hrefWithParam'] = $this->manager->generateUrl($category, $targetPage);
            $data['targetPage'] = $targetPage;
        }

        // Register a function to generate category URL manually
        $data['generateUrl'] = function (PageModel $page, $absolute = false) use ($category) {
            return $this->manager->generateUrl($category, $page, $absolute);
        };

        // Add the image
        if (null !== ($image = $this->manager->getImage($category))) {
            /** @var Controller $controllerAdapter */
            $controllerAdapter = $this->framework->getAdapter(Controller::class);
            $data['image'] = new \stdClass();
            $controllerAdapter->addImageToTemplate($data['image'], ['singleSRC' => $image->path, 'size' => $module->article_categoryImgSize]);
        } else {
            $data['image'] = null;
        }

        return $data;
    }
}
