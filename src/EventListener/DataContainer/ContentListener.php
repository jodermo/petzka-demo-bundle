<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\EventListener\DataContainer;


use Doctrine\DBAL\Connection;

class ContentListener
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * ContentListener constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Get article modules and return them as array.
     *
     * @return array
     */
    public function onGetArticleModules()
    {
        $modules = [];
        $records = $this->db->fetchAll("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type IN ('articlelist') ORDER BY t.name, m.name");

        foreach ($records as $record) {
            $modules[$record['theme']][$record['id']] = \sprintf('%s (ID %s)', $record['name'], $record['id']);
        }

        return $modules;
    }
}
