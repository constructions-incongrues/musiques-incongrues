<?php

/**
 * feeds actions.
 *
 * @package    musiques-incongrues
 * @subpackage feeds
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class feedsActions extends sfActions
{
    /**
     * Generates a podcast feed out of all mixes listed on forum.
     *
     * @param sfWebRequest $request A request object
     */
    public function executePodcast(sfWebRequest $request)
    {
        // Fetch latest mixes
        // TODO : refactor in model
        $q = Doctrine_Query::create()
            ->select('d.name, d.firstcommentid, r.labelname, r.downloadlink, c.body')
            ->from('LUM_Releases r')
            ->innerJoin('r.Discussion d')
            ->where('r.ismix = 1')
            ->andWhere('r.downloadlink is not null')
            ->orderBy('d.DateCreated desc')
            ->limit(50);
        $mixes = $q->execute(null, Doctrine_Core::HYDRATE_ARRAY);
        $q->free();

        // Build
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Feed_Writer_Feed');
        $feed = new Zend_Feed_Writer_Feed();
        $feed->setTitle('Le podcast auto-mécanique des Musiques Incongrues');
        $feed->setLink('http://www.musiques-incongrues.net/forum/releases/?only_mixes=1');
        $feed->setFeedLink('http://www.musiques-incongrues.net/forum/s/feeds/podcast', 'RSS');
        $feed->setDescription('Ce podcast est automatiquement généré à partir de la liste des émissions, mixtapes et autres pièces sonores régulièrement ajoutées au forum des Musiques Incongrues par ses contributeurs.');
        $feed->setDateModified(time());
        foreach ($mixes as $mix)
        {
            $entry = $entry = $feed->createEntry();
            $entry->setTitle($mix['Discussion']['name']);
            // TODO : add slug
            $entry->setLink('http://www.musiques-incongrues.net/forum/discussion/'.$mix['Discussion']['discussionid']);
            // TODO : Make a better joined query
            $comment = Doctrine_Core::getTable('LUM_Comment')->findOneByCommentid($mix['Discussion']['firstcommentid'], Doctrine_Core::HYDRATE_ARRAY);

            // Entry body
            $body = $this->bbParse($comment['body']);
            $entry->setDescription($body);
            $entry->setContent($body);

            if ($mix['labelname'])
            {
                $entry->addAuthor(array('name' => $mix['labelname']));
            }
            $entry->setEnclosure(array('uri' => $mix['downloadlink'], 'type' => 'audio/mpeg', 'length' => 666));
            $feed->addEntry($entry);
        }

        // We don't want those
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);

        // Pass data to view
        $this->feed = $feed;

        // Configure response
        $this->getResponse()->setContentType('application/rss+xml');

        // Select template
        return sfView::SUCCESS;
    }

    // TODO : factor out !
    private function bbParse($string)
    {
        while (preg_match_all('`\[(.+?)=?(.*?)\](.+?)\[/\1\]`', $string, $matches)) foreach ($matches[0] as $key => $match) {
            list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]);
            switch ($tag) {
                case 'b': $replacement = "<strong>$innertext</strong>"; break;
                case 'i': $replacement = "<em>$innertext</em>"; break;
                case 'size': $replacement = "<span style=\"font-size: $param;\">$innertext</span>"; break;
                case 'color': $replacement = "<span style=\"color: $param;\">$innertext</span>"; break;
                case 'center': $replacement = "<div class=\"centered\">$innertext</div>"; break;
                case 'quote': $replacement = "<blockquote>$innertext</blockquote>" . $param? "<cite>$param</cite>" : ''; break;
                case 'url': $replacement = '<a href="' . ($param? $param : $innertext) . "\">$innertext</a>"; break;
                case 'img':
                    $replacement = '<img src="'.$innertext.'" />';
                    break;
            }
            $string = str_replace($match, $replacement, $string);
        }
        return $string;
    }

    /**
     * Generates RSS feeds of latest events.
     *
     * @param sfWebRequest $request
     */
    public function executeEvents(sfWebRequest $request)
    {
        // Fetch latest events
        // TODO : refactor in model
        $q = Doctrine_Query::create()
        ->select('d.name, d.firstcommentid, e.date')
        ->from('LUM_Event e')
        ->innerJoin('e.Discussion d')
        ->orderBy('d.DateCreated desc')
        ->limit(50);
        $events = $q->execute(null, Doctrine_Core::HYDRATE_ARRAY);
        $q->free();

        // Build
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Feed_Writer_Feed');
        $feed = new Zend_Feed_Writer_Feed();
        $feed->setTitle('Ananagenda');
        $feed->setLink('http://www.musiques-incongrues.net/forum/events');
        $feed->setFeedLink('http://www.musiques-incongrues.net/forum/s/feeds/events', 'RSS');
        $feed->setDescription("L'agenda collaboratif du forum des Musiques Incongrues");
        $feed->setDateModified(time());
        foreach ($events as $event)
        {
            $entry = $entry = $feed->createEntry();
            $entry->setTitle($event['Discussion']['name']);
            // TODO : add slug
            $entry->setLink('http://www.musiques-incongrues.net/forum/discussion/'.$event['Discussion']['discussionid']);
            // TODO : Make a better joined query
            $comment = Doctrine_Core::getTable('LUM_Comment')->findOneByCommentid($event['Discussion']['firstcommentid'], Doctrine_Core::HYDRATE_ARRAY);

            // Entry body
            $body = $this->bbParse($comment['body']);
            $entry->setDescription($body);
            $entry->setContent($body);

            $feed->addEntry($entry);
        }

        // We don't want those
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);

        // Pass data to view
        $this->feed = $feed;

        // Configure response
        $this->getResponse()->setContentType('application/rss+xml');

        // Select template
        return sfView::SUCCESS;
    }
}
