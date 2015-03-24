<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;

class BaseControllerTest extends JobBoardTestCase
{
    public function testPostAction()
    {
        $crawler = $this->client->request('GET', '/post');

        $this->assertEquals(1, $crawler->filter('#breadcrumb > li.active:contains("Post a job")')->count());
    }

    public function testPostActionSubmitError()
    {
        $crawler = $this->client->request('GET', '/post');
        $form = $crawler->selectButton('Preview')->form();

        $crawler = $this->client->submit($form, array());

        $this->assertGreaterThan(0, $crawler->filter('#error > ul > li')->count());
    }

    public function testPostActionSubmitSuccess()
    {
        $crawler = $this->client->request('GET', '/post');
        $form = $crawler->selectButton('Preview')->form();

        $this->client->submit($form, [
            'sensiolabs_jobboardbundle_announcement[title]' => 'New job available!',
            'sensiolabs_jobboardbundle_announcement[company]' => 'SensioLabs',
            'sensiolabs_jobboardbundle_announcement[country]' => 'FR',
            'sensiolabs_jobboardbundle_announcement[city]' => 'Paris',
            'sensiolabs_jobboardbundle_announcement[contractType]' => 'FULLTIME',
            'sensiolabs_jobboardbundle_announcement[description]' => 'Lorem ipsum',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/FULLTIME/new-job-available/preview'));
    }

    public function testUpdateAction()
    {
        $announcement = $this->injectAnnouncementInSession();

        $crawler = $this->client->request('GET', '/update');

        $this->assertEquals(1, $crawler->filter('#breadcrumb > li.active:contains("'.$announcement->getTitle().'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[value="'.$announcement->getCity().'"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('textarea:contains("'.$announcement->getDescription().'")')->count());

        $form = $crawler->selectButton('Update')->form();
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->constructAnnouncementUrl($announcement).'/preview'));
    }
}
