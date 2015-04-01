<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\EntityManager;
use SensioLabs\Connect\Security\Authentication\Token\ConnectToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityInteractiveLoginListener implements EventSubscriberInterface
{
    private $em;

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        );
    }

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        //file_put_contents('/tmp/AMOD-LISTENERS.OUT', get_class($event), FILE_APPEND);
        $token = $event->getAuthenticationToken();

        if (!$token instanceof ConnectToken) {
            //file_put_contents('/tmp/AMOD-LISTENERS.OUT', get_class($token), FILE_APPEND);
            return;
        }

        $user = $token->getUser();
        //file_put_contents('/tmp/AMOD-LISTENERS.OUT', var_export($user), FILE_APPEND);
        $user->updateFromConnect($token->getApiUser());

        $this->em->persist($user);
        $this->em->flush();
    }
}
