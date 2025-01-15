<?php

namespace App\Tests;

use App\Service\ApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiTest extends TestCase
{
    private function createHttpClientMock(int $statusCode): HttpClientInterface
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn($statusCode);
        $httpClient->method('request')->willReturn($response);

        return $httpClient;
    }

    public function testGetCapturesByIntervalSuccess(): void
    {
        $httpClient = $this->createHttpClientMock(200);
        $parameterBag = $this->createMock(ParameterBagInterface::class);

        $apiService = new ApiService($httpClient, $parameterBag);
        $result = $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');

        $this->assertIsArray($result);
    }

    public function testGetCapturesByIntervalFailure(): void
    {
        $httpClient = $this->createHttpClientMock(404);
        $parameterBag = $this->createMock(ParameterBagInterface::class);

        $apiService = new ApiService($httpClient, $parameterBag);
        $result = $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');

        $this->assertIsArray($result);
    }

    public function testGetLastCaptureSuccess(): void
    {
        $httpClient = $this->createHttpClientMock(200);
        $parameterBag = $this->createMock(ParameterBagInterface::class);

        $apiService = new ApiService($httpClient, $parameterBag);
        $result = $apiService->getLastCapture('temp', 'sae34bdk1eq2');

        $this->assertIsArray($result);
    }

    public function testGetLastCaptureFailure(): void
    {
        $httpClient = $this->createHttpClientMock(404);
        $parameterBag = $this->createMock(ParameterBagInterface::class);

        $apiService = new ApiService($httpClient, $parameterBag);
        $result = $apiService->getLastCapture('temp', 'sae34bdk1eq2');

        $this->assertIsArray($result);
    }



}