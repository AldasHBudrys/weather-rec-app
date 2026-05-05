<?php

namespace App\Controller;

use App\Service\RecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RecommendationController extends AbstractController
{
    #[Route('/api/recommendations', methods: ['GET'])]
    public function index(
        Request $request,
        RecommendationService $service
    ): JsonResponse {
        $city = $request->query->get('city', 'kaunas');

        try {
            $result = $service->recommend($city);

            return $this->json([
                'city' => $result->city,
                'days' => $result->days,
                'source' => $result->source,
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);

        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ], 500);
        }
    }
}
