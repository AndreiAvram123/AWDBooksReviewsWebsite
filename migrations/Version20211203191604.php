<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211203191604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, image_id INT DEFAULT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, number_of_pages INT NOT NULL, pending TINYINT(1) NOT NULL, declined TINYINT(1) NOT NULL, INDEX IDX_CBE5A331F675F31B (author_id), UNIQUE INDEX UNIQ_CBE5A3313DA5256D (image_id), INDEX IDX_CBE5A33112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_author (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_review (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, front_image_id INT DEFAULT NULL, creator_id INT NOT NULL, pending TINYINT(1) NOT NULL, declined TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, estimated_read_time INT DEFAULT NULL, INDEX IDX_50948A4B16A2B381 (book_id), UNIQUE INDEX UNIQ_50948A4B2DA66B91 (front_image_id), INDEX IDX_50948A4B61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, book_review_id INT NOT NULL, summary LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_9474526C61220EA6 (creator_id), INDEX IDX_9474526C2C8080DD (book_review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_section (id INT AUTO_INCREMENT NOT NULL, book_review_id INT NOT NULL, text LONGTEXT NOT NULL, heading LONGTEXT DEFAULT NULL, INDEX IDX_B69DCE182C8080DD (book_review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_media_hub (id INT AUTO_INCREMENT NOT NULL, facebook_url VARCHAR(255) DEFAULT NULL, instagram_url VARCHAR(255) DEFAULT NULL, linked_in VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_rating (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, book_review_id INT NOT NULL, is_positive_rating TINYINT(1) NOT NULL, INDEX IDX_BDDB3D1F61220EA6 (creator_id), INDEX IDX_BDDB3D1F2C8080DD (book_review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, social_hub_id INT DEFAULT NULL, profile_image_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, nickname VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E97233025D (social_hub_id), UNIQUE INDEX UNIQ_1483A5E9C4CF44DC (profile_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331F675F31B FOREIGN KEY (author_id) REFERENCES book_author (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3313DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33112469DE2 FOREIGN KEY (category_id) REFERENCES book_category (id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B2DA66B91 FOREIGN KEY (front_image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4B61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C2C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id)');
        $this->addSql('ALTER TABLE review_section ADD CONSTRAINT FK_B69DCE182C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id)');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1F61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1F2C8080DD FOREIGN KEY (book_review_id) REFERENCES book_review (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E97233025D FOREIGN KEY (social_hub_id) REFERENCES social_media_hub (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C4CF44DC FOREIGN KEY (profile_image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4B16A2B381');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331F675F31B');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33112469DE2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C2C8080DD');
        $this->addSql('ALTER TABLE review_section DROP FOREIGN KEY FK_B69DCE182C8080DD');
        $this->addSql('ALTER TABLE user_rating DROP FOREIGN KEY FK_BDDB3D1F2C8080DD');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3313DA5256D');
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4B2DA66B91');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9C4CF44DC');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E97233025D');
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4B61220EA6');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C61220EA6');
        $this->addSql('ALTER TABLE user_rating DROP FOREIGN KEY FK_BDDB3D1F61220EA6');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE book_category');
        $this->addSql('DROP TABLE book_review');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE review_section');
        $this->addSql('DROP TABLE social_media_hub');
        $this->addSql('DROP TABLE user_rating');
        $this->addSql('DROP TABLE users');
    }
}
