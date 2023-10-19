<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019143057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, author VARCHAR(128) NOT NULL, nb_views INTEGER NOT NULL, published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO article (id, title, content, created_at, updated_at, author, nb_views, published) SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, author VARCHAR(128) NOT NULL, nb_views INTEGER NOT NULL, published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO article (id, title, content, created_at, updated_at, author, nb_views, published) SELECT id, title, content, created_at, updated_at, author, nb_views, published FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }
}
