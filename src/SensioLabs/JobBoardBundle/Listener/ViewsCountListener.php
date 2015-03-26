<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class ViewsCountListener
{
    protected $enabled = false;

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->enabled || !$entity instanceof Announcement) {
            return;
        }

        $entity->setViewsCount($entity->getViewsCount()+1);

        $em = $args->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
    }
}
