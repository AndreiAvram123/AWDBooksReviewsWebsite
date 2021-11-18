<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211118223009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_media_hub DROP CONSTRAINT fk_3176e1c79be8fd98');
        $this->addSql('ALTER TABLE social_media_hub DROP CONSTRAINT fk_3176e1c79c19920f');
        $this->addSql('DROP SEQUENCE link_id_seq CASCADE');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP INDEX uniq_3176e1c79be8fd98');
        $this->addSql('DROP INDEX uniq_3176e1c79c19920f');
        $this->addSql('ALTER TABLE social_media_hub ADD facebook_url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE social_media_hub ADD instagram_url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE social_media_hub DROP facebook_id');
        $this->addSql('ALTER TABLE social_media_hub DROP instagram_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE link_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE link (id INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE social_media_hub ADD facebook_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_media_hub ADD instagram_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_media_hub DROP facebook_url');
        $this->addSql('ALTER TABLE social_media_hub DROP instagram_url');
        $this->addSql('ALTER TABLE social_media_hub ADD CONSTRAINT fk_3176e1c79be8fd98 FOREIGN KEY (facebook_id) REFERENCES link (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_media_hub ADD CONSTRAINT fk_3176e1c79c19920f FOREIGN KEY (instagram_id) REFERENCES link (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_3176e1c79be8fd98 ON social_media_hub (facebook_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_3176e1c79c19920f ON social_media_hub (instagram_id)');
    }
}
