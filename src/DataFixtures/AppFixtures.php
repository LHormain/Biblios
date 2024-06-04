<?php

namespace App\DataFixtures;

use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\CommentFactory;
use App\Factory\EditorFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // $manager->flush();

        AuthorFactory::createMany(50);
        EditorFactory::createMany(20);
        UserFactory::createMany(15);
        BookFactory::createMany(100);
        CommentFactory::createMany(75);
    }
}
