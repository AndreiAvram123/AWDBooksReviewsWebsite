<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119004210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review ADD front_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B2DA66B91 FOREIGN KEY (front_image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50948A4B2DA66B91 ON book_review (front_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book_review DROP CONSTRAINT FK_50948A4B2DA66B91');
        $this->addSql('DROP INDEX UNIQ_50948A4B2DA66B91');
        $this->addSql('ALTER TABLE book_review DROP front_image_id');
    }
}
