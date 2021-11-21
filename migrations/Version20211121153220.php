<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211121153220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review_section DROP CONSTRAINT fk_b69dce183da5256d');
        $this->addSql('DROP INDEX idx_b69dce183da5256d');
        $this->addSql('ALTER TABLE review_section DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review_section ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review_section ADD CONSTRAINT fk_b69dce183da5256d FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b69dce183da5256d ON review_section (image_id)');
    }
}
