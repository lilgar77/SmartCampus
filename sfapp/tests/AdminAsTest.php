<?php

namespace App\Tests;

use App\Entity\AcquisitionSystem;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminAsTest extends WebTestCase
{
    public function testAjoutDeSaNecessiteAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseRedirects('/403');
    }
    public function testAjoutDeSaAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseIsSuccessful();
    }
    public function testListeDeSaAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $client->request('GET', '/acquisitionsysteme');
        $this->assertResponseIsSuccessful();
    }
    public function testModifDeSaAccessibleAuxAdmins(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $admin = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($admin);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('R2-D2')->getId();
        $client->request('GET', '/acquisitionsyteme/'.$identifier.'/edit');
        $this->assertResponseIsSuccessful();
    }
    public function testAjoutDeSaInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $client->request('GET', '/acquisitionsyteme/add');
        $this->assertResponseRedirects('/403');
    }
    public function testListeDeSaInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $client->request('GET', '/acquisitionsysteme');
        $this->assertResponseRedirects('/403');
    }
    public function testModifDeSaInterditAuxTechniciens(): void
    {
        // https://symfony.com/doc/6.4/testing.html#logging-in-users-authentication

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // le même que dans les fixtures
        $technicien = $userRepository->findOneByEmail('technicien@technicien.com');

        $client->loginUser($technicien);
        $identifier = $client->getContainer()->get('doctrine')->getRepository(AcquisitionSystem::class)->findASByName('R2-D2')->getId();
        $client->request('GET', '/acquisitionsyteme/'.$identifier.'/edit');
        $this->assertResponseRedirects('/403');
    }
}

