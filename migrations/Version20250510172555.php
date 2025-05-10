<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510172555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F13D865311
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_297C98F13D865311 ON rotation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation CHANGE planning_id planning_type_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation ADD CONSTRAINT FK_297C98F11821D178 FOREIGN KEY (planning_type_id) REFERENCES planning_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_297C98F11821D178 ON rotation (planning_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F11821D178
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_297C98F11821D178 ON rotation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation CHANGE planning_type_id planning_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rotation ADD CONSTRAINT FK_297C98F13D865311 FOREIGN KEY (planning_id) REFERENCES planning_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_297C98F13D865311 ON rotation (planning_id)
        SQL);
    }
}
