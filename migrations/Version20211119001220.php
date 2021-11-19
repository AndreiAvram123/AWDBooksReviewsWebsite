<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119001220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP CONSTRAINT fk_c53d045f2c8080dd');
        $this->addSql('DROP INDEX idx_c53d045f2c8080dd');
        $this->addSql('ALTER TABLE image DROP book_review_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE image ADD book_review_id INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_c53d045f2c8080dd FOREIGN KEY (book_review_id) REFERENCES book_review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c53d045f2c8080dd ON image (book_review_id)');
    }
}
