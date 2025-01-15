<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApiService
{
    private HttpClientInterface $client;
    private string $username;
    private string $userpass;
    private FilesystemAdapter $cache;

    /**
     * Constructor to initialize dependencies and load API credentials.
     */
    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->username = $this->getStringParam($parameterBag, 'API_USERNAME');
        $this->userpass = $this->getStringParam($parameterBag, 'API_USERPASS');
        $this->cache = new FilesystemAdapter(); // File system caching
    }

    /**
     * Retrieves a parameter value as a string.
     */
    private function getStringParam(ParameterBagInterface $parameterBag, string $paramName): string
    {
        $value = $parameterBag->get($paramName);
        return is_string($value) ? $value : '';
    }

    /**
     * Fetches captures within a date interval, with caching for performance.
     */
    public function getCapturesByInterval(string $date1, string $date2, string $name, int $page, string $dbname): array
    {
        $cacheKey = 'captures_' . md5($date1 . $date2 . $name . $page . $dbname);

        $cacheItem = $this->cache->getItem($cacheKey);

        // Check if cached data is available and valid
        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            if (isset($cachedData['timestamp']) && (time() - $cachedData['timestamp']) < 120) {
                return $cachedData['data'];
            }
        }

        // If not cached, fetch data from the API
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/interval';

        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $dbname,
            'username' => $this->username,
            'userpass' => $this->userpass,
        ];

        $query = [
            'date1' => $date1,
            'date2' => $date2,
            'nom' => $name,
            'page'  => $page,
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('HTTP error: ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

            // Cache the response with a timestamp
            $cacheItem->set([
                'data' => $validatedResponse,
                'timestamp' => time(),
            ]);
            $cacheItem->expiresAfter(120); // Cache expiry after 120 seconds
            $this->cache->save($cacheItem);

            return $validatedResponse;

        } catch (\Exception $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage());
        }
    }

    /**
     * Fetches the latest capture for a specific room, with caching.
     */
    public function getLastCapture(string $name, string $dbname): array
    {
        $cacheKey = 'last_capture_' . md5($name . $dbname);

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            if (isset($cachedData['timestamp']) && (time() - $cachedData['timestamp']) < 120) {
                return $cachedData['data'];
            }
        }

        // Fetch latest capture from the API
        $url = 'https://sae34.k8s.iut-larochelle.fr/api/captures/last';

        $headers = [
            'accept'   => 'application/ld+json',
            'dbname'   => $dbname,
            'username' => $this->username,
            'userpass' => $this->userpass,
        ];

        $query = [
            'nom' => $name,
            'limit' => 1,
            'page'  => 1,
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query'   => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('HTTP error: ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

            // Cache the response
            $cacheItem->set([
                'data' => $validatedResponse,
                'timestamp' => time(),
            ]);
            $cacheItem->expiresAfter(120);
            $this->cache->save($cacheItem);

            return $validatedResponse;

        } catch (\Exception $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage());
        }
    }

    /**
     * Updates the latest captures for all rooms in the database.
     */
    public function updateLastCapturesForRooms(RoomRepository $roomRepository, EntityManagerInterface $entityManager): void
    {
        $rooms = $roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            $name = $room->getName();
            if ($name === null) {
                continue;
            }

            $roomDbInfo = $roomRepository->getRoomDb($name);
            $dbname = $roomDbInfo['dbname'] ?? null;

            if ($dbname === null) {
                continue;
            }

            try {
                $lastTemperature = $this->getLastCapture('temp', $dbname)[0]['valeur'] ?? null;
                $lastHumidity = $this->getLastCapture('hum', $dbname)[0]['valeur'] ?? null;
                $lastCO2 = $this->getLastCapture('co2', $dbname)[0]['valeur'] ?? null;

                $lastTemperature = is_numeric($lastTemperature) ? (int) $lastTemperature : 0;
                $lastHumidity = is_numeric($lastHumidity) ? (int) $lastHumidity : 0;
                $lastCO2 = is_numeric($lastCO2) ? (int) $lastCO2 : 0;

                $acquisitionSystem = $room->getIdAS();

                if ($acquisitionSystem !== null) {
                    $acquisitionSystem->setTemperature($lastTemperature);
                    $acquisitionSystem->setHumidity($lastHumidity);
                    $acquisitionSystem->setCO2($lastCO2);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $entityManager->flush();
    }

    /**
     * Validates and filters the API response to ensure it contains valid data.
     */
    private function validateResponse(array $response): array
    {
        $validated = [];
        foreach ($response as $item) {
            if (is_array($item) && $this->isAssociativeArray($item)) {
                $validated[] = $item;
            }
        }
        return $validated;
    }

    /**
     * Checks if an array is associative.
     */
    private function isAssociativeArray(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_string($key)) {
                return false;
            }
        }
        return true;
    }
}