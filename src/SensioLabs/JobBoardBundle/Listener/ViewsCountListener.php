<?php

namespace SensioLabs\JobBoardBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class ViewsCountListener
{
    protected $enabled = '';

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (empty($this->enabled) || !$entity instanceof Announcement) {
            return;
        }
        $setter = sprintf('set%sViewsCount', $this->enabled);
        $getter = sprintf('get%sViewsCount', $this->enabled);
        $entity->$setter($entity->$getter()+1);

        $em = $args->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function setEnabled($enabled = '')
    {
        $this->enabled = $enabled;
    }
}
