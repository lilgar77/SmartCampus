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

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->username = $this->getStringParam($parameterBag, 'API_USERNAME');
        $this->userpass = $this->getStringParam($parameterBag, 'API_USERPASS');
        $this->cache = new FilesystemAdapter();
    }

    private function getStringParam(ParameterBagInterface $parameterBag, string $paramName): string
    {
        $value = $parameterBag->get($paramName);
        return is_string($value) ? $value : '';
    }

    /**
     * @param string $date1
     * @param string $date2
     * @param string $name
     * @param int $page
     * @param string $dbname
     *
     * @return array<mixed, mixed>
     */
    public function getCapturesByInterval(
        string $date1,
        string $date2,
        string $name,
        int $page,
        string $dbname
    ): array {
        $cacheKey = 'captures_' . md5($date1 . $date2 . $name . $page . $dbname);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            // Vérification que 'timestamp' et 'data' existent et ont les bons types
            if (is_array($cachedData)&& isset($cachedData['timestamp'], $cachedData['data']) &&
                is_int($cachedData['timestamp']) && is_array($cachedData['data']) &&
                (time() - $cachedData['timestamp']) < 120) {
                return $cachedData['data'];
            }
        }

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
            'page' => $page,
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('HTTP error: ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

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
     * @param string $name
     * @param string $dbname
     *
     * @return array<int, array<string, mixed>>
     * @throws \Exception
     */
    public function getLastCapture(string $name, string $dbname): array
    {
        $cacheKey = 'last_capture_' . md5($name . $dbname);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            // Verify 'timestamp' and 'data' exist and are of correct types
            if (
                is_array($cachedData) && isset($cachedData['timestamp'], $cachedData['data']) &&
                is_int($cachedData['timestamp']) && is_array($cachedData['data']) &&
                (time() - $cachedData['timestamp']) < 120
            ) {
                return $this->ensureCorrectReturnType($cachedData['data']);
            }
        }

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
            'page' => 1,
        ];

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('HTTP error: ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

            // Save validated response to cache
            $cacheItem->set([
                'data' => $validatedResponse,
                'timestamp' => time(),
            ]);
            $cacheItem->expiresAfter(120);
            $this->cache->save($cacheItem);

            return $this->ensureCorrectReturnType($validatedResponse);
        } catch (\Exception $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the response adheres to array<int, array<string, mixed>>.
     *
     * @param array<mixed> $data
     *
     * @return array<int, array<string, mixed>>
     */
    private function ensureCorrectReturnType(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            if (is_array($item) && $this->isAssociativeArray($item)) {
                $result[] = $item;
            }
        }

        // Explicitly cast to the correct type for PHPStan
        /** @var array<int, array<string, mixed>> $result */
        return $result;
    }



    /**
     * @param RoomRepository $roomRepository
     * @param EntityManagerInterface $entityManager
     */
    public function updateLastCapturesForRooms(
        RoomRepository $roomRepository,
        EntityManagerInterface $entityManager
    ): void {
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
                $lastCaptureTemp = $this->getLastCapture('temp', $dbname);
                $lastTemperature = isset($lastCaptureTemp[0]['valeur']) && is_numeric($lastCaptureTemp[0]['valeur'])
                    ? (float) $lastCaptureTemp[0]['valeur']
                    : null;

                $lastCaptureHum = $this->getLastCapture('hum', $dbname);
                $lastHumidity = isset($lastCaptureHum[0]['valeur']) && is_numeric($lastCaptureHum[0]['valeur'])
                    ? (float) $lastCaptureHum[0]['valeur']
                    : null;

                $lastCaptureCO2 = $this->getLastCapture('co2', $dbname);
                $lastCO2 = isset($lastCaptureCO2[0]['valeur']) && is_numeric($lastCaptureCO2[0]['valeur'])
                    ? (float) $lastCaptureCO2[0]['valeur']
                    : null;

                // Ensure default values if null
                $lastTemperature = is_numeric($lastTemperature) ? (int) $lastTemperature : 0;
                $lastHumidity = is_numeric($lastHumidity) ? (int) $lastHumidity : 0;
                $lastCO2 = is_numeric($lastCO2) ? (int) $lastCO2 : 0;

                $acquisitionSystem = $room->getIdAS();

                if ($acquisitionSystem !== null) {
                    $acquisitionSystem->setTemperature($lastTemperature);
                    $acquisitionSystem->setHumidity($lastHumidity);
                    $acquisitionSystem->setCO2($lastCO2);
                }

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
     * @param array<mixed, mixed> $response
     *
     * @return array<mixed, mixed>
     */
    private function validateResponse(array $response): array
    {
        $validated = [];
        foreach ($response as $item) {
            // Vérification que chaque élément est un tableau associatif
            if (is_array($item) && $this->isAssociativeArray($item)) {
                $validated[] = $item;
            }
        }
        return $validated;
    }

    /**
     * @param array<mixed, mixed> $array
     *
     * @return bool
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
