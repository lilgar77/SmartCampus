<?php

namespace App\Tests;

use App\Entity\Building;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminBuildingTest extends WebTestCase
{
    public function testAjoutDeBatimentNecessiteAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/building/add');
        $this->assertResponseRedirects('/login');
    }
    public function testAjoutDeBatimentAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/building/add');
        $this->assertResponseIsSuccessful();
    }
    public function testListeDeBatimentAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/building');
        $this->assertResponseIsSuccessful();
    }
    public function testModifDeSalleAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Informatique')->getId();
        $client->request('GET', '/building/'.$identifier.'/edit');
        $this->assertResponseIsSuccessful();
    }
    public function testAjoutDeSalleInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $client->request('GET', '/building/add');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
    public function testListeDeSalleInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $client->request('GET', '/building');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
    public function testModifDeSalleInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(Building::class)->findBuildingByName('Informatique')->getId();
        $client->request('GET', '/building/'.$identifier.'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}

