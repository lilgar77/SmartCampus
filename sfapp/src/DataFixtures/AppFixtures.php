<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Room;
use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use App\Entity\Building;
use App\Entity\Floor;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $acquisitionSystem = new AcquisitionSystem();
        $acquisitionSystem->setTemperature(20);
        $acquisitionSystem->setCo2(400);
        $acquisitionSystem->setHumidity(50);
        $acquisitionSystem->setName('C3-PO');
        $acquisitionSystem->setWording('Salle de réunion');
        $acquisitionSystem->setMacAdress('00:00:00:00:00:00');
        $acquisitionSystem->setEtat(EtatAS::AVAILABLE);
        $manager->persist($acquisitionSystem);

        $acquisitionSystem2 = new AcquisitionSystem();
        $acquisitionSystem2->setTemperature(25);
        $acquisitionSystem2->setCo2(500);
        $acquisitionSystem2->setHumidity(60);
        $acquisitionSystem2->setWording('Salle de réunion 2');
        $acquisitionSystem2->setMacAdress('00:00:00:00:00:01');
        $acquisitionSystem2->setEtat(EtatAS::INSTALL);
        $manager->persist($acquisitionSystem2);

        $building = new Building();
        $building->setNameBuilding('Informatique');
        $building->setAdressBuilding('LaRochelle');
        $manager->persist($building);

        $building2 = new Building();
        $building2->setNameBuilding('Tech de co');
        $building2->setAdressBuilding('LaRochelle');
        $manager->persist($building2);


        $floor1 = new Floor();
        $floor1->setNumberFloor(0);
        $floor1->setIdBuilding($building);
        $manager->persist($floor1);

        $floor2 = new Floor();
        $floor2->setNumberFloor(1);
        $floor2->setIdBuilding($building);
        $manager->persist($floor2);

        $floor3 = new Floor();
        $floor3->setNumberFloor(2);
        $floor3->setIdBuilding($building);
        $manager->persist($floor3);

        $floor4 = new Floor();
        $floor4->setNumberFloor(3);
        $floor4->setIdBuilding($building);
        $manager->persist($floor4);

        $room = new Room();
        $room->setName('D302');
        $room->setFloor($floor4);
        $room->setBuilding($building);
        $room->setIdAS($acquisitionSystem);

        $manager->persist($room);



        $manager->flush();
    }
}
