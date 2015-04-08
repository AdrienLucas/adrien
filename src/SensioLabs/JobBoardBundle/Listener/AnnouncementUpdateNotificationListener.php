<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AnnouncementUpdateNotificationListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Announcement || !$entity->isValid()) {
            return;
        }

        if ($event->hasChangedField('valid')) {
            $message = \Swift_Message::newInstance()
                //@todo : use the translator. inject it?
                ->setSubject('An announcement has been modified.')
                ->setFrom('send@example.com')
                ->setTo('recipient@example.com')
                ->setBody($this->container->get('templating')->render('SensioLabsJobBoardBundle:Mail:announcementValidateNotification.html.twig', [
                    'announcement' => $entity,
                ]));
            $this->container->get('mailer')->send($message);
        } elseif (
            $event->hasChangedField('title')
            || $event->hasChangedField('city')
            || $event->hasChangedField('description')
            || $event->hasChangedField('contractType')
            || $event->hasChangedField('company')
            || $event->hasChangedField('howToApply')
        ) {
            $message = \Swift_Message::newInstance()
                ->setSubject('An announcement has been modified.')
                ->setFrom('send@example.com')
                ->setTo('recipient@example.com')
                ->setBody($this->container->get('templating')->render('SensioLabsJobBoardBundle:Mail:announcementUpdateNotification.html.twig', [
                    'announcement' => $entity,
                ]));
            $this->container->get('mailer')->send($message);
        }
    }
}
