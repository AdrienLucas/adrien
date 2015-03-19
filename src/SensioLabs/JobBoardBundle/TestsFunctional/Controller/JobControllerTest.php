<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;
use Symfony\Component\DomCrawler\Crawler;

class JobControllerTest extends JobBoardTestCase
{
    public function testPreviewAction()
    {
        $announcement = $this->injectAnnouncementInSession();

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET', $this->constructAnnouncementUrl($announcement).'/preview'
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
}
