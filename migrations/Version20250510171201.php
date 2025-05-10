<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510171201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE rotation (id INT AUTO_INCREMENT NOT NULL, planning_id INT DEFAULT NULL, position INT NOT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_297C98F13D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation ADD CONSTRAINT FK_297C98F13D865311 FOREIGN KEY (planning_id) REFERENCES planning_type (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F13D865311
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rotation
        SQL);
    }
}
