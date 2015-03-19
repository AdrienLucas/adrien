<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;

class JobControllerTest extends WebTestCase
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

    public function testPreviewAction()
    {
        $announcement = new Announcement();
        $announcement->setTitle('test')
            ->setCompany('test')
            ->setCity('test')
            ->setCountry('FR')
            ->setDescription('foobar foobar')
            ->setContractType('FULLTIME');

        $this->client->getContainer()->get('session')->set('announcement_preview', $announcement);

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET', sprintf('/%s/%s/%s/preview',
                $announcement->getCountry(),
                $announcement->getContractType(),
                $announcement->getSlug()
            )
        );

        //Verify if content is present on the page
        $this->assertEquals(1, $crawler->filter('h2:contains("test")')->count());
        $this->assertEquals(2, $crawler->filter('span:contains("test")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("foobar foobar")')->count());

        //Verify if country code has been translated to the country name
        $this->assertGreaterThan(0, $crawler->filter('body:contains("France")')->count());

        //Verify if links have the right href attribute
        $link = $crawler->selectLink('Make changes');
        $this->assertEquals('/update', $link->attr('href'));
        $link = $crawler->selectLink('Publish');
        $this->assertEquals('/order', $link->attr('href'));
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->client = null;
    }
}
