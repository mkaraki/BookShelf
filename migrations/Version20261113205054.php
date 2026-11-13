<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Private mode flag (hidden from anonymous visitors) for authors and publishers.
 */
final class Version20261113205054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add private flag to author and publisher tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE author ADD private TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE publisher ADD private TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE author DROP private');
        $this->addSql('ALTER TABLE publisher DROP private');
    }
}
