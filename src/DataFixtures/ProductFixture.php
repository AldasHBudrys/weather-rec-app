<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Enum\WeatherTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getProducts() as $data) {
            $product = new Product();

            $product->setName($data['name']);
            $product->setCategory($data['category']);
            $product->setPrice((string) $data['price']);
            $product->setSku($data['sku']);
            $product->setTagsFromEnums($data['tags']);

            $manager->persist($product);
        }

        $manager->flush();
    }

    private function getProducts(): array
    {
        return [
            [
                'name' => 'Umbrella',
                'category' => 'outdoor',
                'price' => 15.99,
                'sku' => 'Umb-01',
                'tags' => [WeatherTag::RAIN],
            ],
            [
                'name' => 'Sunscreen',
                'category' => 'health',
                'price' => 12.50,
                'sku' => 'Sus-02',
                'tags' => [WeatherTag::HOT, WeatherTag::CLEAR],
            ],
            [
                'name' => 'Raincoat',
                'category' => 'clothing',
                'price' => 70.00,
                'sku' => 'Rac-03',
                'tags' => [WeatherTag::RAIN, WeatherTag::WIND],
            ],
            [
                'name' => 'Snow Boots',
                'category' => 'clothing',
                'price' => 80.00,
                'sku' => 'Snb-04',
                'tags' => [WeatherTag::SNOW, WeatherTag::COLD],
            ],
            [
                'name' => 'Sunglasses',
                'category' => 'accessories',
                'price' => 25.00,
                'sku' => 'Sug-05',
                'tags' => [WeatherTag::CLEAR],
            ],
            [
                'name' => 'Warm Hat',
                'category' => 'clothing',
                'price' => 30.00,
                'sku' => 'Wah-06',
                'tags' => [WeatherTag::COLD],
            ],
            [
                'name' => 'Warm Gloves',
                'category' => 'clothing',
                'price' => 20.00,
                'sku' => 'Wag-07',
                'tags' => [WeatherTag::COLD],
            ],
            [
                'name' => 'Sun Hat',
                'category' => 'accessories',
                'price' => 18.00,
                'sku' => 'Suh-08',
                'tags' => [WeatherTag::HOT, WeatherTag::CLEAR],
            ],
            [
                'name' => 'Waterproof Backpack',
                'category' => 'outdoor',
                'price' => 75.00,
                'sku' => 'Wab-09',
                'tags' => [WeatherTag::RAIN],
            ],
            [
                'name' => 'Light T-Shirt',
                'category' => 'clothing',
                'price' => 22.00,
                'sku' => 'Lts-10',
                'tags' => [WeatherTag::HOT],
            ],
            [
                'name' => 'Scarf',
                'category' => 'accessories',
                'price' => 15.00,
                'sku' => 'Sca-11',
                'tags' => [WeatherTag::COLD, WeatherTag::WIND],
            ],
            [
                'name' => 'Ski Goggles',
                'category' => 'accessories',
                'price' => 90.00,
                'sku' => 'Skg-12',
                'tags' => [WeatherTag::SNOW, WeatherTag::CLEAR],
            ],
        ];
    }
}
