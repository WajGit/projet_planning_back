<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509133701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type DROP FOREIGN KEY FK_52F7FDBE9C24126
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_52F7FDBE9C24126 ON slot_type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type CHANGE day_id day_type_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type ADD CONSTRAINT FK_52F7FDBE3D89FC11 FOREIGN KEY (day_type_id) REFERENCES day_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52F7FDBE3D89FC11 ON slot_type (day_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type DROP FOREIGN KEY FK_52F7FDBE3D89FC11
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_52F7FDBE3D89FC11 ON slot_type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type CHANGE day_type_id day_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE slot_type ADD CONSTRAINT FK_52F7FDBE9C24126 FOREIGN KEY (day_id) REFERENCES day_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52F7FDBE9C24126 ON slot_type (day_id)
        SQL);
    }
}
