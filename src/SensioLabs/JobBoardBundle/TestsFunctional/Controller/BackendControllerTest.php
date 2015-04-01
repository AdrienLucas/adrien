<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use SensioLabs\JobBoardBundle\Test\JobBoardTestCase;

class BackendControllerTest extends JobBoardTestCase
{
    public function testListAction()
    {
        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUsers',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadAnnouncements',
        ));
        $this->logIn(true);

        $crawler = $this->client->request('GET', '/backend');

        //Verify entities per page
        $this->assertSame(25, $crawler->filter('table tbody tr')->count());

        //Verify pages number
        $this->assertSame(2, $crawler->filter('.backend-pager a')->count());
    }
}
