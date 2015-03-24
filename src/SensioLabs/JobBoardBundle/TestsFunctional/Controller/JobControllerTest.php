<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class JobControllerTest extends JobBoardTestCase
{
    public function testPreviewAction()
    {
        $announcement = $this->injectAnnouncementInSession();

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET', $this->constructAnnouncementUrl($announcement).'/preview'
        );
        file_put_contents('/tmp/amod.out', $this->constructAnnouncementUrl($announcement).'/preview'."\n".$crawler->html());
        //Verify if content is present on the page
        $this->assertEquals(1, $crawler->filter('h2:contains("test")')->count());
        $this->assertEquals(2, $crawler->filter('span:contains("test")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("foobar foobar")')->count());

        //Verify if country code has been translated to the country name
        $this->assertGreaterThan(0, $crawler->filter('body:contains("France")')->count());

        //Verify if links have the right href attribute
        $link = $crawler->selectLink('Make changes');
        $this->assertEquals($this->constructAnnouncementUrl($announcement).'/update', $link->attr('href'));
        $link = $crawler->selectLink('Publish');
        $this->assertEquals('/order', $link->attr('href'));
    }

    public function testUpdateAction()
    {
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->find(1);
        if (!$announcement->getValid()) {
            $announcement->setValid(true);
            $this->em->persist($announcement);
            $this->em->flush();
        }

        $target = $this->constructAnnouncementUrl($announcement).'/update';

        //Test without auth
        /** @var Crawler $crawler */
        $crawler = $this->client->request('GET', $target);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        //Test with auth
        $this->logIn();
        $this->client->enableProfiler();
        $crawler = $this->client->request('GET', $target);

        //Form assertions
        $this->assertEquals(1, $crawler->filter('#breadcrumb > li.active:contains("'.$announcement->getTitle().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[value="'.$announcement->getCity().'"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('textarea:contains("'.$announcement->getDescription().'")')->count());

        $form = $crawler->selectButton('Save')->form();
        $this->client->enableProfiler();
        $this->client->submit($form);

        //Asserting email is sent
        file_put_contents('/tmp/amod.out', $this->client->getResponse()->getContent());
        //echo $this->client->getResponse()->getContent(); die();
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        //var_dump($collectedMessages);
        $message = $collectedMessages[0];

        // Asserting e-mail data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('An announcement has been modified.', $message->getSubject());
        $this->assertTrue(boolval(
            strpos($message->getBody(), $announcement->getTitle())
        ));

        $this->assertTrue($this->client->getResponse()->isRedirect($this->constructAnnouncementUrl($announcement).'/preview'));
    }
}
