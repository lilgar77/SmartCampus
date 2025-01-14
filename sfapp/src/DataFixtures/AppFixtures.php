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
        //need in the test room and floor
        $building = new Building();
        $building->setNameBuilding('Informatique');
        $building->setAdressBuilding('LaRochelle');
        $manager->persist($building);

        //need in the test rooms

        $floor1 = new Floor();
        $floor1->setIdBuilding($building);
        $floor1->setNumberFloor(0);
        $manager->persist($floor1);

        $floor2 = new Floor();
        $floor2->setIdBuilding($building);
        $floor2->setNumberFloor(1);
        $manager->persist($floor2);

        $floor3 = new Floor();
        $floor3->setIdBuilding($building);
        $floor3->setNumberFloor(2);
        $manager->persist($floor3);

        $floor4 = new Floor();
        $floor4->setIdBuilding($building);
        $floor4->setNumberFloor(3);
        $manager->persist($floor4);

        $building2 = new Building();
        $building2->setNameBuilding('Tech de co');
        $building2->setAdressBuilding('LaRochelle');
        $manager->persist($building2);


        $acquisitionSystem = new AcquisitionSystem();
        $acquisitionSystem->setTemperature(0);
        $acquisitionSystem->setCo2(0);
        $acquisitionSystem->setHumidity(0);
        $acquisitionSystem->setName('ESP-11');
        $acquisitionSystem->setWording('Salle de réunion');
        $acquisitionSystem->setMacAdress('00:00:00:00:00:00');
        $acquisitionSystem->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem);

        // room associer
        $room2 = new Room();
        $room2->setName('D304');
        $room2->setFloor($floor4);
        $room2->setBuilding($building);
        $room2->setIdAS($acquisitionSystem);
        $manager->persist($room2);



        $acquisitionSystem2 = new AcquisitionSystem();
        $acquisitionSystem2->setTemperature(0);
        $acquisitionSystem2->setCo2(0);
        $acquisitionSystem2->setHumidity(0);
        $acquisitionSystem2->setName('ESP-004');
        $acquisitionSystem2->setWording('205');
        $acquisitionSystem2->setMacAdress('00:00:00:00:00:01');
        $acquisitionSystem2->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem2);

        $room = new Room();
        $room->setName('D205');
        $room->setFloor($floor3);
        $room->setBuilding($building);
        $room->setIdAS($acquisitionSystem2);
        $manager->persist($room);



        $acquisitionSystem3 = new AcquisitionSystem();
        $acquisitionSystem3->setTemperature(20);
        $acquisitionSystem3->setCo2(400);
        $acquisitionSystem3->setHumidity(50);
        $acquisitionSystem3->setName('ESP-008');
        $acquisitionSystem3->setWording('206');
        $acquisitionSystem3->setMacAdress('00:00:00:00:00:02');
        $acquisitionSystem3->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem3);

        $room3 = new Room();
        $room3->setName('D206');
        $room3->setFloor($floor3);
        $room3->setBuilding($building);
        $room3->setIdAS($acquisitionSystem3);
        $manager->persist($room3);

        $acquisitionSystem4 = new AcquisitionSystem();
        $acquisitionSystem4->setTemperature(0);
        $acquisitionSystem4->setCo2(0);
        $acquisitionSystem4->setHumidity(0);
        $acquisitionSystem4->setName('ESP-006');
        $acquisitionSystem4->setWording('207');
        $acquisitionSystem4->setMacAdress('00:00:00:00:00:03');
        $acquisitionSystem4->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem4);

        $room4 = new Room();
        $room4->setName('D207');
        $room4->setFloor($floor3);
        $room4->setBuilding($building);
        $room4->setIdAS($acquisitionSystem4);
        $manager->persist($room4);

        $acquisitionSystem5 = new AcquisitionSystem();
        $acquisitionSystem5->setTemperature(0);
        $acquisitionSystem5->setCo2(0);
        $acquisitionSystem5->setHumidity(0);
        $acquisitionSystem5->setName('ESP-014');
        $acquisitionSystem5->setWording('204');
        $acquisitionSystem5->setMacAdress('00:00:00:00:00:04');
        $acquisitionSystem5->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem5);

        $room5 = new Room();
        $room5->setName('D204');
        $room5->setFloor($floor3);
        $room5->setBuilding($building);
        $room5->setIdAS($acquisitionSystem5);
        $manager->persist($room5);

        $acquisitionSystem6 = new AcquisitionSystem();
        $acquisitionSystem6->setTemperature(0);
        $acquisitionSystem6->setCo2(0);
        $acquisitionSystem6->setHumidity(0);
        $acquisitionSystem6->setName('ESP-012');
        $acquisitionSystem6->setWording('203');
        $acquisitionSystem6->setMacAdress('00:00:00:00:00:05');
        $acquisitionSystem6->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem6);

        $room6 = new Room();
        $room6->setName('D203');
        $room6->setFloor($floor3);
        $room6->setBuilding($building);
        $room6->setIdAS($acquisitionSystem6);
        $manager->persist($room6);

        $acquisitionSystem7 = new AcquisitionSystem();
        $acquisitionSystem7->setTemperature(0);
        $acquisitionSystem7->setCo2(0);
        $acquisitionSystem7->setHumidity(0);
        $acquisitionSystem7->setName('ESP-005');
        $acquisitionSystem7->setWording('303');
        $acquisitionSystem7->setMacAdress('00:00:00:00:00:06');
        $acquisitionSystem7->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem7);

        $room7 = new Room();
        $room7->setName('D303');
        $room7->setFloor($floor4);
        $room7->setBuilding($building);
        $room7->setIdAS($acquisitionSystem7);
        $manager->persist($room7);


        $acquisitionSystem8 = new AcquisitionSystem();
        $acquisitionSystem8->setTemperature(0);
        $acquisitionSystem8->setCo2(0);
        $acquisitionSystem8->setHumidity(0);
        $acquisitionSystem8->setName('ESP-007');
        $acquisitionSystem8->setWording('C101');
        $acquisitionSystem8->setMacAdress('00:00:00:00:00:07');
        $acquisitionSystem8->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem8);

        $room8 = new Room();
        $room8->setName('C101');
        $room8->setFloor($floor2);
        $room8->setBuilding($building);
        $room8->setIdAS($acquisitionSystem8);
        $manager->persist($room8);

        $acquisitionSystem9 = new AcquisitionSystem();
        $acquisitionSystem9->setTemperature(0);
        $acquisitionSystem9->setCo2(0);
        $acquisitionSystem9->setHumidity(0);
        $acquisitionSystem9->setName('ESP-024');
        $acquisitionSystem9->setWording('D109');
        $acquisitionSystem9->setMacAdress('00:00:00:00:00:08');
        $acquisitionSystem9->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem9);

        $room9 = new Room();
        $room9->setName('D109');
        $room9->setFloor($floor2);
        $room9->setBuilding($building);
        $room9->setIdAS($acquisitionSystem9);
        $manager->persist($room9);

        $acquisitionSystem10 = new AcquisitionSystem();
        $acquisitionSystem10->setTemperature(0);
        $acquisitionSystem10->setCo2(0);
        $acquisitionSystem10->setHumidity(0);
        $acquisitionSystem10->setName('ESP-026');
        $acquisitionSystem10->setWording('Secrétariat');
        $acquisitionSystem10->setMacAdress('00:00:00:00:00:09');
        $acquisitionSystem10->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem10);

        $room10 = new Room();
        $room10->setName('Secrétariat');
        $room10->setFloor($floor2);
        $room10->setBuilding($building);
        $room10->setIdAS($acquisitionSystem10);
        $manager->persist($room10);

        $acquisitionSystem11 = new AcquisitionSystem();
        $acquisitionSystem11->setTemperature(0);
        $acquisitionSystem11->setCo2(0);
        $acquisitionSystem11->setHumidity(0);
        $acquisitionSystem11->setName('ESP-030');
        $acquisitionSystem11->setWording('D001');
        $acquisitionSystem11->setMacAdress('00:00:00:00:00:10');
        $acquisitionSystem11->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem11);

        $room11 = new Room();
        $room11->setName('D001');
        $room11->setFloor($floor1);
        $room11->setBuilding($building);
        $room11->setIdAS($acquisitionSystem11);
        $manager->persist($room11);

        $acquisitionSystem12 = new AcquisitionSystem();
        $acquisitionSystem12->setTemperature(0);
        $acquisitionSystem12->setCo2(0);
        $acquisitionSystem12->setHumidity(0);
        $acquisitionSystem12->setName('ESP-028');
        $acquisitionSystem12->setWording('D002');
        $acquisitionSystem12->setMacAdress('00:00:00:00:00:11');
        $acquisitionSystem12->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem12);

        $room12 = new Room();
        $room12->setName('D002');
        $room12->setFloor($floor1);
        $room12->setBuilding($building);
        $room12->setIdAS($acquisitionSystem12);
        $manager->persist($room12);

        $acquisitionSystem13 = new AcquisitionSystem();
        $acquisitionSystem13->setTemperature(0);
        $acquisitionSystem13->setCo2(0);
        $acquisitionSystem13->setHumidity(0);
        $acquisitionSystem13->setName('ESP-020');
        $acquisitionSystem13->setWording('D004');
        $acquisitionSystem13->setMacAdress('00:00:00:00:00:12');
        $acquisitionSystem13->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem13);

        $room13 = new Room();
        $room13->setName('D004');
        $room13->setFloor($floor1);
        $room13->setBuilding($building);
        $room13->setIdAS($acquisitionSystem13);
        $manager->persist($room13);

        $acquisitionSystem14 = new AcquisitionSystem();
        $acquisitionSystem14->setTemperature(0);
        $acquisitionSystem14->setCo2(0);
        $acquisitionSystem14->setHumidity(0);
        $acquisitionSystem14->setName('ESP-021');
        $acquisitionSystem14->setWording('C004');
        $acquisitionSystem14->setMacAdress('00:00:00:00:00:13');
        $acquisitionSystem14->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem14);

        $room14 = new Room();
        $room14->setName('C004');
        $room14->setFloor($floor1);
        $room14->setBuilding($building);
        $room14->setIdAS($acquisitionSystem14);
        $manager->persist($room14);

        $acquisitionSystem15 = new AcquisitionSystem();
        $acquisitionSystem15->setTemperature(0);
        $acquisitionSystem15->setCo2(0);
        $acquisitionSystem15->setHumidity(0);
        $acquisitionSystem15->setName('ESP-022');
        $acquisitionSystem15->setWording('C007');
        $acquisitionSystem15->setMacAdress('00:00:00:00:00:14');
        $acquisitionSystem15->setEtat(EtatAS::Installer);
        $manager->persist($acquisitionSystem15);

        $room15 = new Room();
        $room15->setName('C007');
        $room15->setFloor($floor1);
        $room15->setBuilding($building);
        $room15->setIdAS($acquisitionSystem15);
        $manager->persist($room15);






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
