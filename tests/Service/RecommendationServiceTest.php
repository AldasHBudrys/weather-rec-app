<?php

namespace App\Tests\Service;

use App\Service\RecommendationService;
use App\Service\LhmtWeatherService;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class RecommendationServiceTest extends TestCase
{
    public function testRecommendReturnsCorrectResult(): void
    {
        $weatherService = $this->createMock(LhmtWeatherService::class);
        $productRepo = $this->createMock(ProductRepository::class);

        $weatherService->method('getCurrentWeather')
            ->with('kaunas')
            ->willReturn([
                'city' => 'Kaunas',
                'days' => [
                    [
                        'time' => '2026-01-01T10:00:00Z',
                        'weatherTags' => ['hot']
                    ]
                ],
                'source' => 'LHMT'
            ]);

        $productRepo->method('findByWeatherTags')
            ->with(['hot'])
            ->willReturn([]);

        $service = new RecommendationService($weatherService, $productRepo);

        $result = $service->recommend('kaunas');

        // DTO access (NOT array)
        $this->assertEquals('Kaunas', $result->city);

        $this->assertEquals([
            [
                'time' => '2026-01-01T10:00:00Z',
                'weatherTags' => ['hot'],
                'products' => []
            ]
        ], $result->days);

        $this->assertEquals('LHMT', $result->source);
    }

    public function testInvalidCityThrowsException(): void
    {
        $weatherService = $this->createMock(LhmtWeatherService::class);
        $productRepo = $this->createMock(ProductRepository::class);

        $weatherService->method('getCurrentWeather')
            ->with('wrongcity')
            ->willThrowException(
                new \InvalidArgumentException('Invalid city provided')
            );

        $productRepo->expects($this->never())
            ->method('findByWeatherTags');

        $service = new RecommendationService($weatherService, $productRepo);

        $this->expectException(\InvalidArgumentException::class);

        $service->recommend('wrongcity');
    }
}
