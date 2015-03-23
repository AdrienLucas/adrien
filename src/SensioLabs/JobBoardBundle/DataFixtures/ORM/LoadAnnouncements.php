<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use  SensioLabs\JobBoardBundle\Entity\Announcement;

class LoadUserData implements FixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $rand = function ($ext) {
            return $ext[rand(0, sizeof($ext)-1)];
        };

        $cities = ['Paris', 'Amiens', 'Lille', 'Clichy', 'Marseille', 'Grenoble'];
        $countries = ['FR', 'GB', 'DE'];

        $contractTypes = Announcement::getContractTypes(false);
        unset($contractTypes['FULLTIME']);
        $contractTypes = array_keys($contractTypes);

        for($i=0;$i<4;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Spanish job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry('ES');
            $announcement->setContractType($rand($contractTypes));

            $manager->persist($announcement);
        }

        for($i=0;$i<3;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Fulltime job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry($rand($countries));
            $announcement->setContractType('FULLTIME');

            $manager->persist($announcement);
        }

        for($i=0;$i<3;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Fulltime spanish job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry('ES');
            $announcement->setContractType('FULLTIME');

            $manager->persist($announcement);
        }

        for($i=0;$i<100;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry($rand($countries));
            $announcement->setContractType($rand($contractTypes));

            $manager->persist($announcement);
        }
        $manager->flush();
    }
}