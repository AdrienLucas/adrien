<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Component\Routing\Router;

class BackendNotification {

    protected $router;
    protected $mailer;

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Announcement) {
            if ($entity->getValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('An announcement has been modified.')
                    ->setFrom('send@example.com')
                    ->setTo('recipient@example.com')
                    ->setBody(sprintf(
                        'Please review the changes made to <a href="%s">%s</a>.',
                        $this->router->generate('backend_edit', ['id' => $entity->getId()]),
                        $entity->getTitle()
                    ));
                $failures = null;
                $ret = $this->mailer->send($message, $failures);
                var_dump($failures);
                var_dump($ret);
            } else {
                echo 'Not valid';
            }
        } else {
            echo 'Not an announcement';
        }
    }

    public function setRouter(Router $router){
        $this->router = $router;
    }

    public function setMailer(\Swift_Mailer $mailer){
        $this->mailer = $mailer;
    }

}