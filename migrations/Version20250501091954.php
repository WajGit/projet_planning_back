<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501091954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE day_type (id INT AUTO_INCREMENT NOT NULL, week_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_27B6BA5FC86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE planning_type (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_44085F67F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE slot_type (id INT AUTO_INCREMENT NOT NULL, day_id INT DEFAULT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_52F7FDBE9C24126 (day_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE week_type (id INT AUTO_INCREMENT NOT NULL, planning_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_46A458991821D178 (planning_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_type ADD CONSTRAINT FK_27B6BA5FC86F3B2F FOREIGN KEY (week_id) REFERENCES week_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planning_type ADD CONSTRAINT FK_44085F67F675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type ADD CONSTRAINT FK_52F7FDBE9C24126 FOREIGN KEY (day_id) REFERENCES day_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week_type ADD CONSTRAINT FK_46A458991821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD planning_type_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C51821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6DC044C51821D178 ON `group` (planning_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C51821D178
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_type DROP FOREIGN KEY FK_27B6BA5FC86F3B2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planning_type DROP FOREIGN KEY FK_44085F67F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type DROP FOREIGN KEY FK_52F7FDBE9C24126
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week_type DROP FOREIGN KEY FK_46A458991821D178
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE planning_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE slot_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE week_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6DC044C51821D178 ON `group`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP planning_type_id
        SQL);
    }
}
