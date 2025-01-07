<?php

namespace App\Tests;

use App\Service\ApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTest extends TestCase
{
    public function testGetCapturesByIntervalSuccess(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(200);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $apiService = new ApiService($mockHttpClient);
        $result = $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');

        $this->assertIsArray($result);

    }

    public function testGetCapturesByIntervalFailure(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(500);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $apiService = new ApiService($mockHttpClient);

        $this->expectException(\Exception::class);
        $apiService->getCapturesByInterval('2025-01-01', '2025-01-06', 'temp', 1, 'sae34bdk1eq2');
    }

    public function testGetLastCaptureSuccess(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(200);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $apiService = new ApiService($mockHttpClient);
        $result = $apiService->getLastCapture('temp', 'sae34bdk1eq2');

        $this->assertIsArray($result);

    }

    public function testGetLastCaptureFailure(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(500);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $apiService = new ApiService($mockHttpClient);

        $this->expectException(\Exception::class);
        $apiService->getLastCapture('temp', 'sae34bdk1eq2');
    }
}
