<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByWeatherTags(array $tags): array
    {
        $results = [];

        foreach ($this->findAll() as $product) {

            $productTags = $product->getTags();
            $tagCount = count($productTags);

            if ($tagCount === 1) {
                if (in_array($productTags[0], $tags, true)) {
                    $results[] = $product;
                }
                continue;
            }

            $matchesAll = count(array_intersect($productTags, $tags)) === $tagCount;

            if ($matchesAll) {
                $results[] = $product;
            }
        }

        return array_slice($results, 0, 2);
    }
}
