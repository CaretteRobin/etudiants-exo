<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260401000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add artist relation and indexes for card filters and pagination';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artist ADD UNIQUE INDEX uniq_artist_external_id (artist_external_id)');
        $this->addSql('ALTER TABLE card ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_161498D3B7970CF8 ON card (artist_id)');
        $this->addSql('CREATE INDEX idx_card_name ON card (name)');
        $this->addSql('CREATE INDEX idx_card_set_code_name ON card (set_code, name)');
        $this->addSql('CREATE INDEX idx_card_artist_name ON card (artist_id, name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_card_artist_name ON card');
        $this->addSql('DROP INDEX idx_card_set_code_name ON card');
        $this->addSql('DROP INDEX idx_card_name ON card');
        $this->addSql('DROP FOREIGN KEY FK_161498D3B7970CF8 ON card');
        $this->addSql('DROP INDEX IDX_161498D3B7970CF8 ON card');
        $this->addSql('ALTER TABLE card DROP artist_id');
        $this->addSql('DROP INDEX uniq_artist_external_id ON artist');
    }
}
