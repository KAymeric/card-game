<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330150836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card (id SERIAL NOT NULL, type_id INT DEFAULT NULL, set_id INT DEFAULT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_161498D3C54C8C93 ON card (type_id)');
        $this->addSql('CREATE INDEX IDX_161498D310FB0D18 ON card (set_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_161498D33DA5256D ON card (image_id)');
        $this->addSql('COMMENT ON COLUMN card.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE custom_media (id SERIAL NOT NULL, realname VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN custom_media.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE set (id SERIAL NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E61425DC3DA5256D ON set (image_id)');
        $this->addSql('COMMENT ON COLUMN set.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE stat (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, value INT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN stat.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE stat_card (stat_id INT NOT NULL, card_id INT NOT NULL, PRIMARY KEY(stat_id, card_id))');
        $this->addSql('CREATE INDEX IDX_375C63879502F0B ON stat_card (stat_id)');
        $this->addSql('CREATE INDEX IDX_375C63874ACC9A20 ON stat_card (card_id)');
        $this->addSql('CREATE TABLE stat_set (stat_id INT NOT NULL, set_id INT NOT NULL, PRIMARY KEY(stat_id, set_id))');
        $this->addSql('CREATE INDEX IDX_1A1BC4E89502F0B ON stat_set (stat_id)');
        $this->addSql('CREATE INDEX IDX_1A1BC4E810FB0D18 ON stat_set (set_id)');
        $this->addSql('CREATE TABLE type (id SERIAL NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(127) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8CDE57293DA5256D ON type (image_id)');
        $this->addSql('COMMENT ON COLUMN type.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D310FB0D18 FOREIGN KEY (set_id) REFERENCES set (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D33DA5256D FOREIGN KEY (image_id) REFERENCES custom_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE set ADD CONSTRAINT FK_E61425DC3DA5256D FOREIGN KEY (image_id) REFERENCES custom_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat_card ADD CONSTRAINT FK_375C63879502F0B FOREIGN KEY (stat_id) REFERENCES stat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat_card ADD CONSTRAINT FK_375C63874ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat_set ADD CONSTRAINT FK_1A1BC4E89502F0B FOREIGN KEY (stat_id) REFERENCES stat (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat_set ADD CONSTRAINT FK_1A1BC4E810FB0D18 FOREIGN KEY (set_id) REFERENCES set (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE57293DA5256D FOREIGN KEY (image_id) REFERENCES custom_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE card DROP CONSTRAINT FK_161498D3C54C8C93');
        $this->addSql('ALTER TABLE card DROP CONSTRAINT FK_161498D310FB0D18');
        $this->addSql('ALTER TABLE card DROP CONSTRAINT FK_161498D33DA5256D');
        $this->addSql('ALTER TABLE set DROP CONSTRAINT FK_E61425DC3DA5256D');
        $this->addSql('ALTER TABLE stat_card DROP CONSTRAINT FK_375C63879502F0B');
        $this->addSql('ALTER TABLE stat_card DROP CONSTRAINT FK_375C63874ACC9A20');
        $this->addSql('ALTER TABLE stat_set DROP CONSTRAINT FK_1A1BC4E89502F0B');
        $this->addSql('ALTER TABLE stat_set DROP CONSTRAINT FK_1A1BC4E810FB0D18');
        $this->addSql('ALTER TABLE type DROP CONSTRAINT FK_8CDE57293DA5256D');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE custom_media');
        $this->addSql('DROP TABLE set');
        $this->addSql('DROP TABLE stat');
        $this->addSql('DROP TABLE stat_card');
        $this->addSql('DROP TABLE stat_set');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE "user"');
    }
}
