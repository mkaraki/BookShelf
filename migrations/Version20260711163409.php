<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260711163409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE owned_book (id INT AUTO_INCREMENT NOT NULL, parent_shelf_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_88CC2127E3DF5711 (parent_shelf_id), INDEX IDX_88CC212716A2B381 (book_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE owned_book ADD CONSTRAINT FK_88CC2127E3DF5711 FOREIGN KEY (parent_shelf_id) REFERENCES shelf (id)');
        $this->addSql('ALTER TABLE owned_book ADD CONSTRAINT FK_88CC212716A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owned_book DROP FOREIGN KEY FK_88CC2127E3DF5711');
        $this->addSql('ALTER TABLE owned_book DROP FOREIGN KEY FK_88CC212716A2B381');
        $this->addSql('DROP TABLE owned_book');
    }
}
