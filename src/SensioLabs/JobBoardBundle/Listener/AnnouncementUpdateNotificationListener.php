<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class AnnouncementUpdateNotificationListener
{
    protected $router;
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Announcement || !$entity->getValid()
            || (
                !$event->hasChangedField('title')
                && !$event->hasChangedField('city')
                && !$event->hasChangedField('description')
                && !$event->hasChangedField('contractType')
                && !$event->hasChangedField('company')
                && !$event->hasChangedField('howToApply')
            )
        ) {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('An announcement has been modified.')
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(sprintf(
                'Please review the changes made to <a href="%s">%s</a>.',
                $this->router->generate('backend_edit', ['id' => $entity->getId()]),
                $entity->getTitle()
            ));
        $this->mailer->send($message);
    }
}
