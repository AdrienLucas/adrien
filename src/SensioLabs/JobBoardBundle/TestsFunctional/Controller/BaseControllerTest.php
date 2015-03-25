<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;

class BaseControllerTest extends JobBoardTestCase
{
    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertSame(10, $crawler->filter('#job-container>div')->count());

        $crawler = $this->client->request('GET', '/?page=2', array(), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));
        $this->assertSame(10, $crawler->filter('body>div')->count());

        $crawler = $this->client->request('GET', '/?page=666', array(), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));
        $this->assertSame(0, $crawler->count());
    }

    public function testIndexActionWithFilters()
    {
        $crawler = $this->client->request('GET', '/?announcement_filters[country]=ES');
        $this->assertSame(7, $crawler->filter('#job-container>div')->count());

        $crawler = $this->client->request('GET', '/?announcement_filters[contractType]=FULLTIME');
        $this->assertSame(6, $crawler->filter('#job-container>div')->count());

        $crawler = $this->client->request('GET', '/?announcement_filters[country]=ES&announcement_filters[contractType]=FULLTIME');
        $this->assertSame(3, $crawler->filter('#job-container>div')->count());
    }

    public function testManageAction()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/manage');
        //Verify entities per page
        $this->assertSame(25, $crawler->filter('table tbody tr')->count());

        //Verify pages number
        $this->assertSame(6, $crawler->filter('.smallpagination>a')->count());

        //Verify links
        $this->assertSame(25, $crawler->filter('a[href$="update"]')->count());
        $this->assertSame(25, $crawler->filter('a[href$="pay"]')->count());

        //Verify delete
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->find(10);
        $this->client->request('GET', $this->constructAnnouncementUrl($announcement).'/delete');
        $this->client->followRedirect();
        $this->assertSame(null, $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->find(10));
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
}
