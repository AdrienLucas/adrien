<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class JobControllerTest extends JobBoardTestCase
{

    public function testShowAction()
    {
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->find(1);

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET', $this->constructAnnouncementUrl($announcement).'/show'
        );

        //Verify if content is present on the page
        $this->assertSame(1, $crawler->filter('h2:contains("'.$announcement->getTitle().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('span:contains("'.$announcement->getCity().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("'.$announcement->getDescription().'")')->count());

        //Verify breadcrumb links
        $this->assertSame(1, $crawler->filter('#breadcrumb a[href$="/?announcement_filters%5Bcountry%5D='.$announcement->getCountry().'"]')->count());
        $this->assertSame(1, $crawler->filter('#breadcrumb a[href$="/?announcement_filters%5BcontractType%5D='.$announcement->getContractType().'"]')->count());
    }

    public function testPreviewAction()
    {
        $announcement = $this->injectAnnouncementInSession();

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'GET', $this->constructAnnouncementUrl($announcement).'/preview'
        );

        //Verify if content is present on the page
        $this->assertSame(1, $crawler->filter('h2:contains("'.$announcement->getTitle().'")')->count());
        $this->assertSame(2, $crawler->filter('span:contains("'.$announcement->getCompany().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("'.$announcement->getDescription().'")')->count());

        //Verify if links have the right href attribute
        $link = $crawler->selectLink('Make changes');
        $this->assertSame($this->constructAnnouncementUrl($announcement).'/update', $link->attr('href'));
        $link = $crawler->selectLink('Publish');
        $this->assertSame('/order', $link->attr('href'));
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
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        //Test with auth
        $this->logIn();
        $this->client->enableProfiler();
        $crawler = $this->client->request('GET', $target);

        //Form assertions
        $this->assertSame(1, $crawler->filter('#breadcrumb > li.active:contains("'.$announcement->getTitle().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[value="'.$announcement->getCity().'"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('textarea:contains("'.$announcement->getDescription().'")')->count());

        $form = $crawler->selectButton('Save')->form();
        $this->client->enableProfiler();
        $this->client->submit($form);

        //Asserting email is sent
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting e-mail data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('An announcement has been modified.', $message->getSubject());
        $this->assertTrue(boolval(
            strpos($message->getBody(), $announcement->getTitle())
        ));

        $this->assertTrue($this->client->getResponse()->isRedirect($this->constructAnnouncementUrl($announcement).'/preview'));
    }
}
