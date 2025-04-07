<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407053041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO app.category (id, name, created_at, updated_at, value_code) VALUES 
             (1, 'Toys', '2025-04-07 10:00:2', '2025-04-07 10:00:2', '1222233391'),
             (2, 'TV', '2025-04-06 10:00:2', '2025-04-03 10:00:2', '9876543214'),
             (3, 'Phone', '2025-04-01 10:00:2', '2025-04-02 10:00:2', '1029384756')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
