<?php

namespace App\Tests\Service;

use App\Service\RecommendationService;
use App\Service\LhmtWeatherService;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class RecommendationServiceTest extends TestCase
{
    public function testRecommendReturnsDtoStructure(): void
    {
        $weatherService = $this->createMock(LhmtWeatherService::class);
        $productRepo = $this->createMock(ProductRepository::class);

        $weatherService->expects($this->once())
            ->method('getCurrentWeather')
            ->with('kaunas')
            ->willReturn([
                'city' => 'Kaunas',
                'weatherTags' => ['hot'],
                'source' => 'LHMT'
            ]);

        $productRepo->expects($this->once())
            ->method('findByWeatherTags')
            ->with(['hot'])
            ->willReturn([]);

        $service = new RecommendationService($weatherService, $productRepo);

        $result = $service->recommend('kaunas');

        $this->assertEquals('Kaunas', $result->city);
        $this->assertEquals(['hot'], $result->weatherForecast);
        $this->assertEquals([], $result->products);
        $this->assertEquals('LHMT', $result->source);
    }

    public function testInvalidCityThrowsException(): void
    {
        $weatherService = $this->createMock(LhmtWeatherService::class);
        $productRepo = $this->createMock(ProductRepository::class);

        $weatherService->expects($this->once())
            ->method('getCurrentWeather')
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
