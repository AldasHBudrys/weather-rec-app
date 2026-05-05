<?php

namespace App\Controller;

use App\Service\LhmtWeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/api/weather', methods: ['GET'])]
    public function getWeather(
        Request $request,
        LhmtWeatherService $weatherService
    ): JsonResponse {
        $city = $request->query->get('city', 'kaunas');

        try {
            $weather = $weatherService->getCurrentWeather($city);

            return $this->json($weather);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Failed to fetch weather data',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
