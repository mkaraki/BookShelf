<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260711162523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, author_read VARCHAR(255) DEFAULT NULL, disambiguation LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, book_read VARCHAR(255) DEFAULT NULL, isbn BIGINT DEFAULT NULL, disambiguation LONGTEXT DEFAULT NULL, publisher_id INT DEFAULT NULL, INDEX IDX_CBE5A33140C86FCE (publisher_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE book_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9478D34516A2B381 (book_id), INDEX IDX_9478D345F675F31B (author_id), PRIMARY KEY (book_id, author_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE book_case (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, parent_room_id INT NOT NULL, INDEX IDX_6195D20C265F04D5 (parent_room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE publisher (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, publisher_read VARCHAR(255) DEFAULT NULL, disambiguation LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, room_floor INT DEFAULT NULL, parent_site_id INT NOT NULL, INDEX IDX_729F519B84F56200 (parent_site_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE shelf (id INT AUTO_INCREMENT NOT NULL, shelf_number INT NOT NULL, parent_book_case_id INT NOT NULL, INDEX IDX_A5475BE3C5EA14D (parent_book_case_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33140C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_case ADD CONSTRAINT FK_6195D20C265F04D5 FOREIGN KEY (parent_room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B84F56200 FOREIGN KEY (parent_site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE shelf ADD CONSTRAINT FK_A5475BE3C5EA14D FOREIGN KEY (parent_book_case_id) REFERENCES book_case (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33140C86FCE');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE book_case DROP FOREIGN KEY FK_6195D20C265F04D5');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B84F56200');
        $this->addSql('ALTER TABLE shelf DROP FOREIGN KEY FK_A5475BE3C5EA14D');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE book_case');
        $this->addSql('DROP TABLE publisher');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE shelf');
    }
}
