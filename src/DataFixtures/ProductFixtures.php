<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'IPhone',
                'price' => 170_000,
            ],
            [
                'name' => 'Headphones',
                'price' => 10_000,
            ],
            [
                'name' => 'Case',
                'price' => 3500,
            ],
        ];

        foreach ($products as $product) {
            $product = (new Product())
                ->setName(name: $product['name'])
                ->setPrice(price: $product['price'])
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }
}
