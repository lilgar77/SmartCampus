<?php

namespace App\Tests;

use App\Entity\Floor;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminFloorTest extends WebTestCase
{
    public function testAjoutEtageNecessiteAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/floor/add');
        $this->assertResponseRedirects('/login');
    }
    public function testAjoutEtageAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/floor/add');
        $this->assertResponseIsSuccessful();
    }
    public function testListeEtageAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/floor');
        $this->assertResponseIsSuccessful();
    }
    public function testModifEtageAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(Floor::class)->findFloorByNumber(1)->getId();
        $client->request('GET', '/floor/'.$identifier.'/edit');
        $this->assertResponseIsSuccessful();
    }
}