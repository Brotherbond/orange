<?php

declare (strict_types = 1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reviews-stats',
    description: 'Displays the day or month with the highest number of reviews.',
)]
class ReviewsStatsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Displays the day or month with the highest number of reviews.')
            ->addOption(
                'month',
                null,
                InputOption::VALUE_NONE,
                'Display the month with the highest number of reviews.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isMonthOption = $input->getOption('month');

        $groupBy = $isMonthOption ? 'EXTRACT(YEAR FROM rev.published_at), EXTRACT(MONTH FROM rev.published_at)' : 'rev.published_at';

        $sql = "
            SELECT $groupBy AS date_group, COUNT(rev.id) AS review_count
            FROM review rev
            GROUP BY $groupBy
            ORDER BY review_count DESC, date_group DESC
            LIMIT 1
        ";

        $result = $this->connection->fetchAssociative($sql);

        if ($result === false) {
            $output->writeln('No reviews found.');
            return Command::FAILURE;
        }

        if ($isMonthOption) {
            $year = $result['extract'];
            $month = $result['date_group'];
            $formattedDate = sprintf('%d-%02d', $year, $month);
        } else {
            $formattedDate = $result['date_group'];
        }

        $reviewCount = $result['review_count'];

        $output->writeln(sprintf(
            'The %s with the highest number of reviews is: %s with %d reviews.',
            $isMonthOption ? 'month' : 'day',
            $formattedDate,
            $reviewCount
        ));

        return Command::SUCCESS;
    }
}
