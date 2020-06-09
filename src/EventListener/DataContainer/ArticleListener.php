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
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Haste\Model\Relations;

class ArticleListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * ArticleListener constructor.
     *
     * @param Connection        $db
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(Connection $db, PermissionChecker $permissionChecker)
    {
        $this->db = $db;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * On data container load. Limit the categories set in the article archive settings.
     *
     * @param DataContainer $dc
     */
    public function onLoadCallback(DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        /** @var Input $input */
        $input = $this->framework->getAdapter(Input::class);

        if (!$categories || 0 === \count($categories = StringUtil::deserialize($categories, true))) {
            return;
        }

        $GLOBALS['TL_DCA'][$dc->table]['fields']['categories']['eval']['rootNodes'] = $categories;
    }

    /**
     * On submit record. Update the category relations.
     *
     * @param DataContainer $dc
     */
    public function onSubmitCallback(DataContainer $dc)
    {
        // Return if the user is allowed to assign categories or the record is not new
        if ($this->permissionChecker->canUserAssignCategories() || $dc->activeRecord->tstamp > 0) {
            return;
        }

        $dc->field = 'categories';

        $relations = new Relations();
        $relations->updateRelatedRecords($this->permissionChecker->getUserDefaultCategories(), $dc);

        // Reset back the field property
        $dc->field = null;
    }

    /**
     * On categories options callback
     *
     * @return array
     */
    public function onCategoriesOptionsCallback()
    {
        /** @var Input $input */
        $input = $this->framework->getAdapter(Input::class);

        // Do not generate the options for other views than listings
        if ($input->get('act') && $input->get('act') !== 'select') {
            return [];
        }

        return $this->generateOptionsRecursively();
    }

    /**
     * Generate the options recursively
     *
     * @param int    $pid
     * @param string $prefix
     *
     * @return array
     */
    private function generateOptionsRecursively($pid = 0, $prefix = '')
    {
        $options = [];
        $records = $this->db->fetchAll('SELECT * FROM tl_article_category WHERE pid=? ORDER BY sorting', [$pid]);

        foreach ($records as $record) {
            $options[$record['id']] = $prefix . $record['title'];

            foreach ($this->generateOptionsRecursively($record['id'], $record['title'] . ' / ') as $k => $v) {
                $options[$k] = $v;
            }
        }

        return $options;
    }
}
