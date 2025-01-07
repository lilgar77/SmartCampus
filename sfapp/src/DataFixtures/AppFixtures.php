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

        // Création des bâtiments
        $building = new Building();
        $building->setNameBuilding('Informatique');
        $building->setAdressBuilding('LaRochelle');
        $manager->persist($building);

        $building2 = new Building();
        $building2->setNameBuilding('Tech de co');
        $building2->setAdressBuilding('LaRochelle');
        $manager->persist($building2);

// Création des étages et des salles
        for ($floorNumber = 0; $floorNumber <= 3; $floorNumber++) {
            $floor = new Floor();
            $floor->setNumberFloor($floorNumber);
            $floor->setIdBuilding($building);
            $manager->persist($floor);

            // Création des salles pour chaque étage
            for ($roomNumber = 1; $roomNumber <= 8; $roomNumber++) {
                $room = new Room();
                $room->setName('D' . $floorNumber . '0' . $roomNumber); // Exemple : D101, D102, etc.
                $room->setFloor($floor);
                $room->setBuilding($building);

                // Association avec un système d'acquisition si nécessaire
                    $acquisitionSystem = new AcquisitionSystem();
                    $acquisitionSystem->setTemperature(20 + $floorNumber);
                    $acquisitionSystem->setCo2(400 + $roomNumber * 10);
                    $acquisitionSystem->setHumidity(50 + $roomNumber);
                    $acquisitionSystem->setName('AS-' . $floorNumber . '-' . $roomNumber);
                    $acquisitionSystem->setWording('Salle ' . $room->getName());
                    $acquisitionSystem->setMacAdress('00:00:00:00:0' . $floorNumber . ':' . $roomNumber);
                    $acquisitionSystem->setEtat(EtatAS::Installer);
                    $manager->persist($acquisitionSystem);

                    $room->setIdAS($acquisitionSystem);

                $manager->persist($room);
            }
        }
        for ($floorNumber = 0; $floorNumber <= 3; $floorNumber++) {
            $floor = new Floor();
            $floor->setNumberFloor($floorNumber);
            $floor->setIdBuilding($building2);
            $manager->persist($floor);

            // Création des salles pour chaque étage
            for ($roomNumber = 1; $roomNumber <= 8; $roomNumber++) {
                $room = new Room();
                $room->setName('C' . $floorNumber . '0' . $roomNumber); // Exemple : D101, D102, etc.
                $room->setFloor($floor);
                $room->setBuilding($building2);

                // Association avec un système d'acquisition si nécessaire
                $acquisitionSystem = new AcquisitionSystem();
                $acquisitionSystem->setTemperature(20 + $floorNumber);
                $acquisitionSystem->setCo2(400 + $roomNumber * 10);
                $acquisitionSystem->setHumidity(50 + $roomNumber);
                $acquisitionSystem->setName('ASC-' . $floorNumber . '-' . $roomNumber);
                $acquisitionSystem->setWording('Salle ' . $room->getName());
                $acquisitionSystem->setMacAdress('00:00:00:00:1' . $floorNumber . ':' . $roomNumber);
                $acquisitionSystem->setEtat(EtatAS::Disponible);
                $manager->persist($acquisitionSystem);

                $room->setIdAS($acquisitionSystem);

                $manager->persist($room);
            }
        }

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
