<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321233718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE division_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE illustration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, author_id INT NOT NULL, review_id INT NOT NULL, text TEXT NOT NULL, time TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C3E2E969B ON comment (review_id)');
        $this->addSql('COMMENT ON COLUMN comment.time IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE division (id INT NOT NULL, name VARCHAR(25) DEFAULT \'\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE illustration (id INT NOT NULL, review_id INT DEFAULT NULL, img VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D67B9A423E2E969B ON illustration (review_id)');
        $this->addSql('CREATE TABLE rating (id INT NOT NULL, review_id INT NOT NULL, valuer_id INT NOT NULL, value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D88926223E2E969B ON rating (review_id)');
        $this->addSql('CREATE INDEX IDX_D889262267EC383F ON rating (valuer_id)');
        $this->addSql('CREATE TABLE review (id INT NOT NULL, group_id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, text TEXT NOT NULL, date_of_publication TIMESTAMP(0) WITH TIME ZONE NOT NULL, author_rating INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6FE54D947 ON review (group_id)');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('COMMENT ON COLUMN review.date_of_publication IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE review_tag (review_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(review_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_FBEBB4A3E2E969B ON review_tag (review_id)');
        $this->addSql('CREATE INDEX IDX_FBEBB4ABAD26311 ON review_tag (tag_id)');
        $this->addSql('CREATE TABLE review_user (review_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(review_id, user_id))');
        $this->addSql('CREATE INDEX IDX_6F279B513E2E969B ON review_user (review_id)');
        $this->addSql('CREATE INDEX IDX_6F279B51A76ED395 ON review_user (user_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, nickname VARCHAR(100) NOT NULL, google_id VARCHAR(255) DEFAULT NULL, yandex_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3E2E969B FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A423E2E969B FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926223E2E969B FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262267EC383F FOREIGN KEY (valuer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6FE54D947 FOREIGN KEY (group_id) REFERENCES division (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_tag ADD CONSTRAINT FK_FBEBB4A3E2E969B FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_tag ADD CONSTRAINT FK_FBEBB4ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_user ADD CONSTRAINT FK_6F279B513E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_user ADD CONSTRAINT FK_6F279B51A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6FE54D947');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C3E2E969B');
        $this->addSql('ALTER TABLE illustration DROP CONSTRAINT FK_D67B9A423E2E969B');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D88926223E2E969B');
        $this->addSql('ALTER TABLE review_tag DROP CONSTRAINT FK_FBEBB4A3E2E969B');
        $this->addSql('ALTER TABLE review_user DROP CONSTRAINT FK_6F279B513E2E969B');
        $this->addSql('ALTER TABLE review_tag DROP CONSTRAINT FK_FBEBB4ABAD26311');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D889262267EC383F');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE review_user DROP CONSTRAINT FK_6F279B51A76ED395');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE division_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE illustration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rating_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE division');
        $this->addSql('DROP TABLE illustration');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_tag');
        $this->addSql('DROP TABLE review_user');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE "user"');
    }
}
