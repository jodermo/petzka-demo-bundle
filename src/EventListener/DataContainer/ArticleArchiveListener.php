<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\EventListener\DataContainer;

use Petzka\DemoBundle\PermissionChecker;

class ArticleArchiveListener
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * ArticleArchiveListener constructor.
     *
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * On data container load.
     */
    public function onLoadCallback()
    {
        if (!$this->permissionChecker->canUserManageCategories()) {
            unset($GLOBALS['TL_DCA']['tl_article_archive']['list']['global_operations']['categories']);
        }
    }
}
