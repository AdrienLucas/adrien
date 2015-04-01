<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;

class BaseControllerTest extends JobBoardTestCase
{
    public function testIndexAction()
    {
        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUsers',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadAnnouncements',
        ));
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
        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUsers',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadAnnouncements',
        ));

        $crawler = $this->client->request('GET', '/?announcement_filters[country]=ES');
        $this->assertGreaterThanOrEqual(2, $crawler->filter('#job-container>div')->count());

        $crawler = $this->client->request('GET', '/?announcement_filters[country]=ES&announcement_filters[contractType]=FULLTIME');
        $this->assertGreaterThanOrEqual(1, $crawler->filter('#job-container>div')->count());


        $crawler = $this->client->request('GET', '/?announcement_filters[contractType]=FULLTIME');
        $this->assertSame(2, $crawler->filter('#job-container>div')->count());
    }

    public function testManageAction()
    {
        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUsers',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadAnnouncements',
        ));
        $this->logIn();
        $crawler = $this->client->request('GET', '/manage');
        //Verify entities per page
        $this->assertSame(25, $crawler->filter('table tbody tr')->count());

        //Verify pages number
        $this->assertSame(3, $crawler->filter('.smallpagination>a')->count());

        //Verify links
        $this->assertSame(25, $crawler->filter('a[href$="update"]')->count());
        $this->assertSame(25, $crawler->filter('a[href$="pay"]')->count());

        //Verify delete
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBySlug('sarah-s-announcement-no-30');
        $this->client->request('GET', $this->constructAnnouncementUrl($announcement).'/delete');
        //$this->client->followRedirect();
        //$this->assertSame(null, $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBySlug('sarah-s-announcement-no-30'));
    }

    public function testViewsCountIncrement()
    {
        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUsers',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadAnnouncements',
        ));
        //$announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->findBySlug('');
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBySlug('sarah-s-valid-announcement');
        $origCount = $announcement->getViewsCount();

        $this->client->request('GET', '/');

        $this->em->refresh($announcement);
        $this->assertSame($origCount+1, $announcement->getViewsCount());

        $this->client->request('GET', $this->constructAnnouncementUrl($announcement));
        $announcement = $this->em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBySlug('sarah-s-valid-announcement');

        $this->assertSame($origCount+2, $announcement->getViewsCount());
    }

    public function testSLNBar()
    {
        $this->logIn();
        $this->client->request('GET', '/manage');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}
