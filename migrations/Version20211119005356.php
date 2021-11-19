<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119005356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review_section ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review_section ADD CONSTRAINT FK_B69DCE183DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B69DCE183DA5256D ON review_section (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review_section DROP CONSTRAINT FK_B69DCE183DA5256D');
        $this->addSql('DROP INDEX IDX_B69DCE183DA5256D');
        $this->addSql('ALTER TABLE review_section DROP image_id');
    }
}
