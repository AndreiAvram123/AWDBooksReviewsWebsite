<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112213823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE book (id INT NOT NULL, title VARCHAR(255) NOT NULL, number_of_pages INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE book_review DROP title');
        $this->addSql('ALTER TABLE book_review DROP summary');
        $this->addSql('ALTER TABLE book_review RENAME COLUMN number_of_pages TO book_id');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50948A4B16A2B381 ON book_review (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book_review DROP CONSTRAINT FK_50948A4B16A2B381');
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP INDEX IDX_50948A4B16A2B381');
        $this->addSql('ALTER TABLE book_review ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book_review ADD summary TEXT NOT NULL');
        $this->addSql('ALTER TABLE book_review RENAME COLUMN book_id TO number_of_pages');
    }
}
