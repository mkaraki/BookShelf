<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260926155014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Private mode flag (hidden from anonymous visitors)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE book_case ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE owned_book ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE room ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE shelf ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE site ADD private TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP private');
        $this->addSql('ALTER TABLE book_case DROP private');
        $this->addSql('ALTER TABLE owned_book DROP private');
        $this->addSql('ALTER TABLE room DROP private');
        $this->addSql('ALTER TABLE shelf DROP private');
        $this->addSql('ALTER TABLE site DROP private');
    }
}
