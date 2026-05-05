<?php

namespace App\Controller;

use App\Service\RecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/api/recommendations', methods: ['GET'])]
    public function recommend(
        Request $request,
        RecommendationService $service
    ): JsonResponse {
        $city = $request->query->get('city', 'kaunas');

        return $this->json(
            $service->recommend($city)
        );
    }
}
