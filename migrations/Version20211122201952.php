<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122201952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_rating ADD book_review_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1F2C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BDDB3D1F2C8080DD ON user_rating (book_review_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_rating DROP CONSTRAINT FK_BDDB3D1F2C8080DD');
        $this->addSql('DROP INDEX IDX_BDDB3D1F2C8080DD');
        $this->addSql('ALTER TABLE user_rating DROP book_review_id');
    }
}
