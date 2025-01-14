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
        $this->cache = new FilesystemAdapter();  // Utilisation du cache sur le système de fichiers
    }

    private function getStringParam(ParameterBagInterface $parameterBag, string $paramName): string
    {
        $value = $parameterBag->get($paramName);
        return is_string($value) ? $value : '';
    }

    /**
     * Récupère les captures par intervalle avec cache.
     */
    public function getCapturesByInterval(string $date1, string $date2, string $name, int $page, string $dbname): array
    {
        $cacheKey = 'captures_' . md5($date1 . $date2 . $name . $page . $dbname);  // Clé de cache unique

        // Récupérer l'élément de cache
        $cacheItem = $this->cache->getItem($cacheKey);

        // Vérifier si l'élément est dans le cache et s'il n'est pas expiré
        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            // Vérifier l'expiration manuellement (si les données datent de moins de 10 minutes)
            if (isset($cachedData['timestamp']) && (time() - $cachedData['timestamp']) < 600) {
                return $cachedData['data']; // Retourne les données en cache si elles ne sont pas expirées
            }
        }

        // Si les données sont expirées ou non présentes dans le cache, on refait la requête API
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
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

            // Enregistrer les données dans le cache avec un timestamp
            $cacheItem->set([
                'data' => $validatedResponse,
                'timestamp' => time(), // Ajouter un timestamp pour savoir quand les données ont été mises en cache
            ]);
            $cacheItem->expiresAfter(60);  // Expiration après 10 minutes (600 secondes)
            $this->cache->save($cacheItem);

            return $validatedResponse;

        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    /**
     * Récupère la dernière capture pour une salle avec cache.
     */
    public function getLastCapture(string $name, string $dbname): array
    {
        $cacheKey = 'last_capture_' . md5($name . $dbname);  // Clé de cache unique

        // Récupérer l'élément de cache
        $cacheItem = $this->cache->getItem($cacheKey);

        // Vérifier si l'élément est dans le cache et s'il n'est pas expiré
        if ($cacheItem->isHit()) {
            $cachedData = $cacheItem->get();
            // Vérifier l'expiration manuellement (si les données datent de moins de 10 minutes)
            if (isset($cachedData['timestamp']) && (time() - $cachedData['timestamp']) < 600) {
                return $cachedData['data']; // Retourne les données en cache si elles ne sont pas expirées
            }
        }

        // Si les données sont expirées ou non présentes dans le cache, on refait la requête API
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
                throw new \Exception('Erreur HTTP : ' . $response->getStatusCode());
            }

            $responseData = $response->toArray();
            $validatedResponse = $this->validateResponse($responseData);

            // Enregistrer les données dans le cache avec un timestamp
            $cacheItem->set([
                'data' => $validatedResponse,
                'timestamp' => time(), // Ajouter un timestamp pour savoir quand les données ont été mises en cache
            ]);
            $cacheItem->expiresAfter(60);  // Expiration après 10 minutes (600 secondes)
            $this->cache->save($cacheItem);

            return $validatedResponse;

        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la requête : ' . $e->getMessage());
        }
    }

    /**
     * Mise à jour des dernières captures pour les salles avec cache.
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
     * Validates and transforms the API response to ensure it matches the expected type.
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
     * Helper function to check if an array is associative with string keys.
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