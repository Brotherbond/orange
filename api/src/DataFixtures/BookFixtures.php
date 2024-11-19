<?php

declare (strict_types = 1);

namespace App\DataFixtures;

use App\Entity\Book;
use App\Enum\PromotionStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $book = new Book();
            $book->setTitle("Book Title $i");
            $book->setAuthor("Author $i");
            $book->setBookUrl("https://example.com/book-$i.json");
            $book->setPromotionStatus(PromotionStatus::None);
            $book->setSlug('book-' . Uuid::v4()->toRfc4122());

            $manager->persist($book);
        }

        $manager->flush();
    }
}
