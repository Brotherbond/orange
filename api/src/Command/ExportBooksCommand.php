<?php

declare (strict_types = 1);

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:export-books',
    description: 'Add a short description for your command',
)]
class ExportBooksCommand extends Command
{
    protected static $defaultName = 'app:export-books';
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Exports books to a JSON file.')
            ->addArgument('filePath', InputArgument::REQUIRED, 'The path to save the JSON file.');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');

        $books = $this->entityManager->getRepository(Book::class)->findAll();

        if (!$books) {
            $output->writeln('<error>No books found to export.</error>');
            return Command::FAILURE;
        }

        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'author' => $book->getAuthor(),
                'title' => $book->getTitle(),
                'categories' => $book->getCategories()->map(fn($category) => $category->getName())->toArray(),
                'reviews' => count($book->getReviews()),
                'bookmarks' => count($book->getBookmarks()),
                'activeUsers' => $this->getActiveUsers($book),
            ];
        }

        try {
            if (!is_writable(dirname($filePath))) {
                $output->writeln('<error>Invalid file path: directory is not writable.</error>');
                return Command::FAILURE;
            }
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($jsonData === false) {
                $output->writeln('<error>JSON encoding error: ' . json_last_error_msg() . '</error>');
                return Command::FAILURE;
            }
            file_put_contents($filePath, $jsonData);
            $output->writeln("<info>Books exported successfully to $filePath</info>");
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to write to file: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getActiveUsers(Book $book): array
    {
        $reviewUsers = $book->getReviews()->map(fn($review) => $review->getUser()->getEmail())->toArray();
        $bookmarkUsers = $book->getBookmarks()->map(fn($bookmark) => $bookmark->getUser()->getEmail())->toArray();

        return array_values(array_intersect($reviewUsers, $bookmarkUsers));
    }
}
