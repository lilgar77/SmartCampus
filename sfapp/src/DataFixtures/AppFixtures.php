<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Room;
use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $acquisitionSystem = new AcquisitionSystem();
        $acquisitionSystem->setTemperature(20);
        $acquisitionSystem->setCo2(400);
        $acquisitionSystem->setHumidity(50);
        $acquisitionSystem->setWording('Salle de réunion');
        $acquisitionSystem->setMacAdress('00:00:00:00:00:00');
        $acquisitionSystem->setEtat(EtatAS::AVAILABLE);

        $manager->persist($acquisitionSystem);

        $room = new Room();
        $room->setName('D302');
        $room->setIdAS($acquisitionSystem);

        $manager->persist($room);

        $manager->flush();
    }
}
