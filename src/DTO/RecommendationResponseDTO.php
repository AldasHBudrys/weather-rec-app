<?php

namespace App\DTO;

class RecommendationResponseDTO
{
    public function __construct(
        public string $city,
        public array $days,
        public string $source
    ) {
    }
}
