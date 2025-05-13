<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511160941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_6EA9A146A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE day (id INT AUTO_INCREMENT NOT NULL, week_id INT DEFAULT NULL, name DATETIME NOT NULL, INDEX IDX_E5A02990C86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, day_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_AC0E20679C24126 (day_id), INDEX IDX_AC0E20678C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE week (id INT AUTO_INCREMENT NOT NULL, calendar_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5B5A69C0A40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day ADD CONSTRAINT FK_E5A02990C86F3B2F FOREIGN KEY (week_id) REFERENCES week (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot ADD CONSTRAINT FK_AC0E20679C24126 FOREIGN KEY (day_id) REFERENCES day (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot ADD CONSTRAINT FK_AC0E20678C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week ADD CONSTRAINT FK_5B5A69C0A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD calendar_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6DC044C5A40A2C8 ON `group` (calendar_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5A40A2C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day DROP FOREIGN KEY FK_E5A02990C86F3B2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20679C24126
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20678C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week DROP FOREIGN KEY FK_5B5A69C0A40A2C8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE week
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6DC044C5A40A2C8 ON `group`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP calendar_id
        SQL);
    }
}
