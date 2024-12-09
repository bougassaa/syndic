<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209092054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_depense ADD syndic_id INT NOT NULL');
        $this->addSql('ALTER TABLE type_depense ADD CONSTRAINT FK_1C24F8A2F0654A02 FOREIGN KEY (syndic_id) REFERENCES syndic (id)');
        $this->addSql('CREATE INDEX IDX_1C24F8A2F0654A02 ON type_depense (syndic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_depense DROP FOREIGN KEY FK_1C24F8A2F0654A02');
        $this->addSql('DROP INDEX IDX_1C24F8A2F0654A02 ON type_depense');
        $this->addSql('ALTER TABLE type_depense DROP syndic_id');
    }
}
