<?php

namespace SensioLabs\JobBoardBundle\TestsFunctional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseControllerTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testPostAction()
    {
        $crawler = $this->client->request('GET', '/post');

        $this->assertGreaterThan(0, $crawler->filter('#breadcrumb > li.active:contains("Post a job")')->count());
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

        $this->assertTrue($this->client->getResponse()->isRedirect('/preview'));
    }
}
