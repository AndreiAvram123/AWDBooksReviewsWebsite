<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119125400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rating (id INT NOT NULL, likes INT NOT NULL, dislikes INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE book_review ADD rating_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4BA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50948A4BA32EFC6 ON book_review (rating_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book_review DROP CONSTRAINT FK_50948A4BA32EFC6');
        $this->addSql('DROP SEQUENCE rating_id_seq CASCADE');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP INDEX UNIQ_50948A4BA32EFC6');
        $this->addSql('ALTER TABLE book_review DROP rating_id');
    }
}
