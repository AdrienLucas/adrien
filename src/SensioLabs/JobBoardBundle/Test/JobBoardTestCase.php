<?php

namespace SensioLabs\JobBoardBundle\Test;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class JobBoardTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->client = null;
    }

    /**
     * @return Announcement
     */
    protected function injectAnnouncementInSession()
    {
        $announcement = new Announcement();
        $announcement->setTitle('test')
            ->setCompany('test')
            ->setCity('test')
            ->setCountry('FR')
            ->setDescription('foobar foobar')
            ->setContractType('FULLTIME');

        $this->client->getContainer()->get('session')->set('announcement_preview', $announcement);

        return $announcement;
    }

    /**
     * @param Announcement $announcement
     *
     * @return string
     */
    protected function constructAnnouncementUrl(Announcement $announcement)
    {
        return sprintf('/%s/%s/%s',
            $announcement->getCountry(),
            $announcement->getContractType(),
            $announcement->getSlug()
        );
    }
}
