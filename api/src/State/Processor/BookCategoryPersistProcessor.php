<?php

namespace App\Processor;

use App\Entity\Book;
use App\Entity\BookCategory;
use Doctrine\ORM\EntityManagerInterface;

class BookCategoryPersistProcessor
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Associates categories with a book.
     *
     * @param Book   $book       The book entity
     * @param array  $categories Array of category IDs to associate
     */
    public function process(Book $book, array $categories): void
    {
        foreach ($categories as $categoryId) {
            $category = $this->entityManager->getRepository(BookCategory::class)->find($categoryId);
            if (!$category) {
                throw new \InvalidArgumentException("Category with ID $categoryId not found.");
            }

            $book->addCategory($category);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
