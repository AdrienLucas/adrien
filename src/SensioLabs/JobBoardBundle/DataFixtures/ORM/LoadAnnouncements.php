<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class LoadAnnouncements implements FixtureInterface
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

        $announcement = new Announcement();
        $announcement->setTitle('toto job announcement');
        $announcement->setCity($rand($cities));
        $announcement->setCompany('totoSensioLabs');
        $announcement->setDescription('totoLorem ipsum');
        $announcement->setCountry('FR');
        $announcement->setContractType($rand($contractTypes));
        $announcement->setUser('8332d6be-089e-46ff-9608-981cc0089ba3');
        $announcement->setValid(1);
        $manager->persist($announcement);

        for ($i = 0;$i<4;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Spanish job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry('ES');
            $announcement->setContractType($rand($contractTypes));
            $announcement->setUser('8332d6be-089e-46ff-9608-981cc0089ba3');

            $manager->persist($announcement);
        }

        for ($i = 0;$i<3;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Fulltime job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry($rand($countries));
            $announcement->setContractType('FULLTIME');
            $announcement->setUser('8332d6be-089e-46ff-9608-981cc0089ba3');

            $manager->persist($announcement);
        }

        for ($i = 0;$i<3;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Fulltime spanish job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry('ES');
            $announcement->setContractType('FULLTIME');
            $announcement->setUser('8332d6be-089e-46ff-9608-981cc0089ba3');

            $manager->persist($announcement);
        }

        for ($i = 0;$i<100;$i++) {
            $announcement = new Announcement();
            $announcement->setTitle('Job announcement #'.$i);
            $announcement->setCity($rand($cities));
            $announcement->setCompany('SensioLabs');
            $announcement->setDescription('Lorem ipsum');
            $announcement->setCountry($rand($countries));
            $announcement->setContractType($rand($contractTypes));
            $announcement->setUser('8332d6be-089e-46ff-9608-981cc0089ba3');
            $manager->persist($announcement);
        }
        $manager->flush();
    }
}
