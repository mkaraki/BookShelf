<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:import-bookshelf-v1',
    description: 'Import BookShelf v1 library',
)]
class ImportCommand extends Command
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Exported file')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // SymfonyStyle provides a beautiful interactive interface
        $io = new SymfonyStyle($input, $output);
        $io->title('BookShelf v1 import utility');

        $io->warning('This application must run on fresh (after migrated) database and application.');

        $continue = $io->confirm('Are you sure you want to continue?', true);
        if (!$continue) {
            return Command::FAILURE;
        }

        $file = $input->getArgument('file');
        $content = file_get_contents($file);
        $json_data = json_decode($content, true);
        if ($json_data === null) {
            $io->error('Invalid JSON file');
            return Command::FAILURE;
        }

        $entityManager = $this->entityManager;
        $conn = $entityManager->getConnection();
        $conn->beginTransaction();
        $io->info('Transaction started');

        try {
            foreach ($io->progressIterate($json_data['sites']) as $site) {
                // Process each site
                $conn->executeStatement('INSERT INTO site (id, name) VALUES (:id, :name)', [
                        'id' => $site['siteId'],
                        'name' => $site['siteName'],
                    ]);
            }

            foreach ($io->progressIterate($json_data['rooms']) as $room) {
                // Process each room
                $conn->executeStatement('INSERT INTO room (id, name, room_floor, parent_site_id)
                                              VALUES (:id, :name, :room_floor, :parent_site_id)', [
                        'id' => $room['roomId'],
                        'name' => $room['roomName'],
                        'room_floor' => $room['roomFloor'],
                        'parent_site_id' => $room['parentSite'],
                    ]);
            }

            foreach ($io->progressIterate($json_data['cases']) as $case) {
                // Process each case
                $conn->executeStatement('INSERT INTO book_case (id, name, parent_room_id)
                                              VALUES (:id, :name, :parent_room_id)', [
                        'id' => $case['caseId'],
                        'name' => $case['caseName'],
                        'parent_room_id' => $case['parentRoom'],
                    ]);
            }

            foreach ($io->progressIterate($json_data['shelves']) as $shelf) {
                // Process each shelf
                $conn->executeStatement('INSERT INTO shelf (id, shelf_number, parent_book_case_id)
                                              VALUES (:id, :shelf_number, :parent_book_case_id)', [
                        'id' => $shelf['shelfId'],
                        'shelf_number' => $shelf['shelfNumber'],
                        'parent_book_case_id' => $shelf['parentCase'],
                    ]);
            }

            foreach ($io->progressIterate($json_data['authors']) as $author) {
                // Process each author
                $conn->executeStatement('INSERT INTO author (id, name, author_read, disambiguation)
                                              VALUES (:id, :name, :author_read, :disambiguation)', [
                        'id' => $author['authorId'],
                        'name' => $author['authorName'],
                        'author_read' => $author['authorRead'],
                        'disambiguation' => $author['authorDisambiguation'],
                    ]);
            }

            foreach ($io->progressIterate($json_data['publishers']) as $publisher) {
                // Process each publisher
                $conn->executeStatement('INSERT INTO publisher (id, name, publisher_read, disambiguation)
                                              VALUES (:id, :name, :publisher_read, :disambiguation)', [
                    'id' => $publisher['publisherId'],
                    'name' => $publisher['publisherName'],
                    'publisher_read' => $publisher['publisherRead'],
                    'disambiguation' => $publisher['publisherDisambiguation'],
                ]);
            }

            foreach ($io->progressIterate($json_data['bookCollection']) as $book) {
                // Process each book
                $conn->executeStatement('INSERT INTO book (id, name, book_read, disambiguation, isbn, publisher_id)
                                              VALUES (:id, :name, :book_read, :disambiguation, :isbn, :publisher_id)', [
                    'id' => $book['uniqueBookId'],
                    'name' => $book['bookName'],
                    'book_read' => $book['bookRead'],
                    'disambiguation' => $book['bookDisambiguation'],
                    'isbn' => $book['isbn'],
                    'publisher_id' => $book['publisherId'],
                ]);

                $conn->executeStatement('INSERT INTO owned_book (id, parent_shelf_id, book_id)
                                              VALUES (:id, :parent_shelf_id, :book_id)', [
                    'id' => $book['uniqueBookId'],
                    'parent_shelf_id' => $book['belongShelf'],
                    'book_id' => $book['uniqueBookId'],
                ]);

                foreach ($book['authorIds'] as $authorId) {
                    $conn->executeStatement('INSERT INTO book_author (book_id, author_id)
                                                  VALUES (:book_id, :author_id)', [
                        'book_id' => $book['uniqueBookId'],
                        'author_id' => $authorId,
                    ]);
                }
            }

            $conn->commit();
        }
        catch (\Throwable $t) {
            $conn->rollback();
            throw $t;
        }

        $io->success('Library imported successfully!');

        return Command::SUCCESS;
    }
}
