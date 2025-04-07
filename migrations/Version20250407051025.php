<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407051025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, value_code VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_64C19C1DEFA6B41 (value_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8
        SQL
        );
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS product (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, price_currency VARCHAR(3) NOT NULL, price_whole INT NOT NULL, price_rest INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8
        SQL
        );
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS product_category (category_id INT NOT NULL, product_id CHAR(36) NOT NULL, PRIMARY KEY(category_id, product_id)) DEFAULT CHARACTER SET utf8
        SQL
        );
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL
        );
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL
        );
        $this->addSql(<<<'SQL'
            DROP TABLE product_category
        SQL
        );
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL
        );
    }
}
