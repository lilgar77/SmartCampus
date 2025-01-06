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
        $acquisitionSystem->setEtat(EtatAS::Disponible);
        $manager->persist($acquisitionSystem);

        $acquisitionSystem2 = new AcquisitionSystem();
        $acquisitionSystem2->setTemperature(25);
        $acquisitionSystem2->setCo2(500);
        $acquisitionSystem2->setHumidity(60);
        $acquisitionSystem2->setName('R2-D2');
        $acquisitionSystem2->setWording('Salle de réunion 2');
        $acquisitionSystem2->setMacAdress('00:00:00:00:00:01');
        $acquisitionSystem2->setEtat(EtatAS::Disponible);
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
        $room->setIdAS($acquisitionSystem2);

        $room2 = new Room();
        $room2->setName('D304');
        $room2->setFloor($floor4);
        $room2->setBuilding($building);
        $room2->setIdAS($acquisitionSystem);

        $manager->persist($room);
        $manager->persist($room2);

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

        $alert1 = new Alert();
        $alert1->setIdSA($acquisitionSystem);
        $alert1->setIdRoom($room2);
        $alert1->setType(AlertType::temp);
        $alert1->setDateBegin(new \DateTime('now'));
        $alert1->setDescription("Il fait beaucoup trop chaud");
        $manager->persist($alert1);

        $alert2 = new Alert();
        $alert2->setIdSA($acquisitionSystem);
        $alert2->setIdRoom($room2);
        $alert2->setType(AlertType::hum);
        $alert2->setDateBegin(new \DateTime('now'));
        $alert2->setDescription("Il fait très humide dans la salle");
        $manager->persist($alert2);


        $manager->flush();
    }
}
