<?php

namespace SensioLabs\JobBoardBundle\Test;

use Doctrine\ORM\EntityManager;
use SensioLabs\Connect\Security\Authentication\Token\ConnectToken;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use SensioLabs\Internal\Connect\Api\Entity\User as ApiUser;
use SensioLabs\JobBoardBundle\Entity\User;

class JobBoardTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->client = null;

        if(!is_null($this->em)) {
            $this->em->close();
            $this->em = null;
        }
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

    protected function logIn($asAdmin = false)
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $apiuser = new ApiUser();
        $apiuser->set('uuid', '8332d6be-089e-46ff-9608-981cc0089ba3');
        $user = $this->em->getRepository('SensioLabsJobBoardBundle:User')->findOneByUsername($asAdmin ? 'adrienlucas' : 'sarahkhalil');

        $token = new ConnectToken($user, 'xxxx', $apiuser, 'xxxx', null, array($asAdmin ? 'ROLE_ADMIN' : 'ROLE_USER', 'ROLE_CONNECT_USER'));

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
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
