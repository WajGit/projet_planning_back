<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601201104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE group_employee (group_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_54AF39DEFE54D947 (group_id), INDEX IDX_54AF39DE8C03F15C (employee_id), PRIMARY KEY(group_id, employee_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_employee ADD CONSTRAINT FK_54AF39DEFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_employee ADD CONSTRAINT FK_54AF39DE8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE group_employee DROP FOREIGN KEY FK_54AF39DEFE54D947
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE group_employee DROP FOREIGN KEY FK_54AF39DE8C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE group_employee
        SQL);
    }
}
