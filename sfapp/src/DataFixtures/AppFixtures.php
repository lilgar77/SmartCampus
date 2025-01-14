<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Room;
use App\Entity\AcquisitionSystem;
use App\Model\EtatAS;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\User;
use App\Entity\Alert;
use App\Model\AlertType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        $acquisitionSystem = new AcquisitionSystem();
        $acquisitionSystem->setTemperature(20);
        $acquisitionSystem->setCo2(400);
        $acquisitionSystem->setHumidity(50);
        $acquisitionSystem->setName('ESP-11');
        $acquisitionSystem->setWording('Salle de réunion');
        $acquisitionSystem->setMacAdress('00:00:00:00:00:00');
        $acquisitionSystem->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem);

        $acquisitionSystem2 = new AcquisitionSystem();
        $acquisitionSystem2->setTemperature(25);
        $acquisitionSystem2->setCo2(500);
        $acquisitionSystem2->setHumidity(60);
        $acquisitionSystem2->setName('R2-D2');
        $acquisitionSystem2->setWording('Salle de réunion 2');
        $acquisitionSystem2->setMacAdress('00:00:00:00:00:01');
        $acquisitionSystem2->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem2);

        $acquisitionSystem3 = new AcquisitionSystem();
        $acquisitionSystem3->setTemperature(20);
        $acquisitionSystem3->setCo2(400);
        $acquisitionSystem3->setHumidity(50);
        $acquisitionSystem3->setName('ESP-008');
        $acquisitionSystem3->setWording('Salle de réunion');
        $acquisitionSystem3->setMacAdress('00:00:00:00:00:02');
        $acquisitionSystem3->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem3);

        //need in the test room and floor
        $building = new Building();
        $building->setNameBuilding('Informatique');
        $building->setAdressBuilding('LaRochelle');
        $manager->persist($building);

        //need in the test rooms
        $floor3 = new Floor();
        $floor3->setIdBuilding($building);
        $floor3->setNumberFloor(2);
        $manager->persist($floor3);

        $building2 = new Building();
        $building2->setNameBuilding('Tech de co');
        $building2->setAdressBuilding('LaRochelle');
        $manager->persist($building2);

        $floor1 = new Floor();
        $floor1->setIdBuilding($building);
        $floor1->setNumberFloor(0);
        $manager->persist($floor1);

        $floor2 = new Floor();
        $floor2->setIdBuilding($building);
        $floor2->setNumberFloor(1);
        $manager->persist($floor2);



        $floor4 = new Floor();
        $floor4->setIdBuilding($building);
        $floor4->setNumberFloor(3);
        $manager->persist($floor4);

        $room2 = new Room();
        $room2->setName('D304');
        $room2->setFloor($floor4);
        $room2->setBuilding($building);
        $room2->setIdAS($acquisitionSystem);
        $manager->persist($room2);

        $room3 = new Room();
        $room3->setName('D206');
        $room3->setFloor($floor3);
        $room3->setBuilding($building);
        $room3->setIdAS($acquisitionSystem3);
        $manager->persist($room3);


        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setUsername('admin');
        $plaintextPassword = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('technicien@technicien.com');
        $user->setUsername('technicien');
        $plaintextPassword = 'technicien';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_TECHNICIEN']);
        $manager->persist($user);


        $manager->flush();
    }
}
