<?php

namespace App\Processor;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookCategoryRemoveProcessor
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Removes all categories associated with a book.
     *
     * @param Book $book The book entity
     */
    public function process(Book $book): void
    {
        foreach ($book->getCategories() as $category) {
            $book->removeCategory($category);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
