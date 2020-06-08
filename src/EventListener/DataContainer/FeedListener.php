<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\EventListener\DataContainer;

use Petzka\DemoBundle\FeedGenerator;
use Contao\Automator;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Haste\Model\Model;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FeedListener implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * FeedListener constructor.
     *
     * @param Connection       $db
     * @param SessionInterface $session
     */
    public function __construct(Connection $db, SessionInterface $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    /**
     * On data container load callback.
     *
     * @param DataContainer $dc
     */
    public function onLoadCallback(DataContainer $dc)
    {
        switch ($dc->table) {
            case 'tl_content':
            case 'tl_article':
            case 'tl_article_archive':
            case 'tl_article_category':
                $this->generateFeed('generateFeedsByArchive');
                break;
            case 'tl_article_feed':
                $this->generateFeed('generateFeed');
                break;
        }
    }

    /**
     * On data container submit callback.
     *
     * @param DataContainer $dc
     */
    public function onSubmitCallback(DataContainer $dc)
    {
        // Schedule a article feed update
        if ('tl_article_category' === $dc->table && $dc->id) {
            /** @var Model $modelAdapter */
            $modelAdapter = $this->framework->getAdapter(Model::class);

            $articleIds = $modelAdapter->getReferenceValues('tl_article', 'categories', $dc->id);
            $articleIds = \array_map('intval', \array_unique($articleIds));

            if (\count($articleIds) > 0) {
                $archiveIds = $this->db
                    ->executeQuery('SELECT DISTINCT(pid) FROM tl_article WHERE id IN ('.\implode(',', $articleIds).')')
                    ->fetchAll(\PDO::FETCH_COLUMN, 0);

                $session = $this->session->get('article_feed_updater');
                $session = \array_merge((array) $session, $archiveIds);
                $this->session->set('article_feed_updater', \array_unique($session));
            }
        }
    }

    /**
     * Generate the feed.
     *
     * @param string $method
     */
    private function generateFeed($method)
    {
        $session = $this->session->get('article_feed_updater');

        if (!\is_array($session) || empty($session)) {
            return;
        }

        $feedGenerator = new FeedGenerator();

        foreach ($session as $id) {
            $feedGenerator->$method($id);
        }

        (new Automator())->generateSitemap();

        $this->session->set('article_feed_updater', null);
    }
}
