<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use Hautelook\AliceBundle\Alice\DataFixtureLoader;

class LoadAnnouncements extends DataFixtureLoader
{
    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__.'/../Announcements.yml',
        );
    }
}
