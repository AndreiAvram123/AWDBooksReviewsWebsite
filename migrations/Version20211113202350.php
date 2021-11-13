<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211113202350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review ADD creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50948A4B61220EA6 ON book_review (creator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book_review DROP CONSTRAINT FK_50948A4B61220EA6');
        $this->addSql('DROP INDEX IDX_50948A4B61220EA6');
        $this->addSql('ALTER TABLE book_review DROP creator_id');
    }
}
