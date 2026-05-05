<?php

namespace App\DTO;

class RecommendationResponseDTO
{
    public function __construct(
        public string $city,
        public array $weatherForecast,
        public array $products,
        public string $source
    ) {
    }
}
