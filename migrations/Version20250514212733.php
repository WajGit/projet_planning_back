<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514212733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_6EA9A146A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4FBF094FF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE day (id INT AUTO_INCREMENT NOT NULL, week_id INT DEFAULT NULL, name DATETIME NOT NULL, INDEX IDX_E5A02990C86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE day_type (id INT AUTO_INCREMENT NOT NULL, week_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_27B6BA5FC86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE employee_company (employee_id INT NOT NULL, company_id INT NOT NULL, INDEX IDX_CFF35F408C03F15C (employee_id), INDEX IDX_CFF35F40979B1AD6 (company_id), PRIMARY KEY(employee_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, planning_type_id INT DEFAULT NULL, calendar_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start TIME NOT NULL, end TIME NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_6DC044C5979B1AD6 (company_id), UNIQUE INDEX UNIQ_6DC044C51821D178 (planning_type_id), UNIQUE INDEX UNIQ_6DC044C5A40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE planning_type (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_44085F67F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rotation (id INT AUTO_INCREMENT NOT NULL, planning_type_id INT DEFAULT NULL, position INT NOT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_297C98F11821D178 (planning_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, day_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_AC0E20679C24126 (day_id), INDEX IDX_AC0E20678C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE slot_type (id INT AUTO_INCREMENT NOT NULL, day_type_id INT DEFAULT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_52F7FDBE3D89FC11 (day_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE week (id INT AUTO_INCREMENT NOT NULL, calendar_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, year INT NOT NULL, number INT NOT NULL, INDEX IDX_5B5A69C0A40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE week_type (id INT AUTO_INCREMENT NOT NULL, planning_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_46A458991821D178 (planning_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day ADD CONSTRAINT FK_E5A02990C86F3B2F FOREIGN KEY (week_id) REFERENCES week (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_type ADD CONSTRAINT FK_27B6BA5FC86F3B2F FOREIGN KEY (week_id) REFERENCES week_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employee_company ADD CONSTRAINT FK_CFF35F408C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employee_company ADD CONSTRAINT FK_CFF35F40979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C51821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planning_type ADD CONSTRAINT FK_44085F67F675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation ADD CONSTRAINT FK_297C98F11821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot ADD CONSTRAINT FK_AC0E20679C24126 FOREIGN KEY (day_id) REFERENCES day (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot ADD CONSTRAINT FK_AC0E20678C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type ADD CONSTRAINT FK_52F7FDBE3D89FC11 FOREIGN KEY (day_type_id) REFERENCES day_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week ADD CONSTRAINT FK_5B5A69C0A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week_type ADD CONSTRAINT FK_46A458991821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day DROP FOREIGN KEY FK_E5A02990C86F3B2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_type DROP FOREIGN KEY FK_27B6BA5FC86F3B2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employee_company DROP FOREIGN KEY FK_CFF35F408C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employee_company DROP FOREIGN KEY FK_CFF35F40979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C51821D178
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5A40A2C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planning_type DROP FOREIGN KEY FK_44085F67F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F11821D178
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20679C24126
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20678C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type DROP FOREIGN KEY FK_52F7FDBE3D89FC11
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week DROP FOREIGN KEY FK_5B5A69C0A40A2C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE week_type DROP FOREIGN KEY FK_46A458991821D178
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE employee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE employee_company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `group`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE planning_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE slot_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE week
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE week_type
        SQL);
    }
}
